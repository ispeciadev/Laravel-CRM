<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ispecia\Voip\Contracts\VoipProvider as VoipProviderContract;

class VoipProvider extends Model implements VoipProviderContract
{
    protected $table = 'voip_providers';

    protected $fillable = [
        'name',
        'driver',
        'config',
        'is_active',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'config' => 'encrypted:array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when provider is saved or deleted
        static::saved(function ($provider) {
            Cache::forget('active_voip_provider');
        });

        static::deleted(function ($provider) {
            Cache::forget('active_voip_provider');
        });
    }

    /**
     * Scope to get active provider.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('priority', 'desc');
    }

    /**
     * Activate this provider and deactivate all others.
     */
    public function activate(): void
    {
        DB::transaction(function () {
            // Deactivate all other providers
            static::where('id', '!=', $this->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Activate this provider
            $this->is_active = true;
            $this->save();

            // Clear cache
            Cache::forget('active_voip_provider');
        });
    }

    /**
     * Deactivate this provider.
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
        
        Cache::forget('active_voip_provider');
    }

    /**
     * Get a specific config value.
     */
    public function getConfigValue(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Validate that required config keys exist for this driver.
     */
    public function validateConfig(): array
    {
        $errors = [];
        $requiredKeys = $this->getRequiredConfigKeys();

        foreach ($requiredKeys as $key => $label) {
            if (empty($this->config[$key])) {
                $errors[$key] = "The {$label} field is required.";
            }
        }

        return $errors;
    }

    /**
     * Get required config keys based on driver.
     */
    protected function getRequiredConfigKeys(): array
    {
        return match ($this->driver) {
            'twilio' => [
                'account_sid' => 'Account SID',
                'auth_token' => 'Auth Token',
                'from_number' => 'From Number',
            ],
            'telnyx' => [
                'api_key' => 'API Key',
                'connection_id' => 'Connection ID',
                'from_number' => 'From Number',
            ],
            'sip' => [
                'sip_server' => 'SIP Server',
                'username' => 'Username',
                'password' => 'Password',
            ],
            default => [],
        };
    }

    /**
     * Get driver display name.
     */
    public function getDriverDisplayName(): string
    {
        return match ($this->driver) {
            'twilio' => 'Twilio',
            'telnyx' => 'Telnyx',
            'sip' => 'Generic SIP',
            default => ucfirst($this->driver),
        };
    }

    /**
     * Get the trunks for this provider.
     */
    public function trunks()
    {
        return $this->hasMany(\Ispecia\Voip\Models\VoipTrunk::class, 'voip_provider_id');
    }

    /**
     * Check if this is the active provider.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
