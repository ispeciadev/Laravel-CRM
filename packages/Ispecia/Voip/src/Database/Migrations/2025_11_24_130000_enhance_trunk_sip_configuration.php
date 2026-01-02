<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('voip_trunks', function (Blueprint $table) {
            // Authentication method: username_password or ip_auth
            $table->enum('auth_method', ['username_password', 'ip_auth'])
                ->default('username_password')
                ->after('voip_provider_id');
            
            // SIP Username for username/password authentication
            $table->string('sip_username')->nullable()->after('auth_method');
            
            // SIP Password for username/password authentication (will be encrypted)
            $table->text('sip_password')->nullable()->after('sip_username');
            
            // Allowed IPs/Subnets for IP-based authentication (stored as JSON)
            $table->text('allowed_ips')->nullable()->after('sip_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voip_trunks', function (Blueprint $table) {
            $table->dropColumn([
                'auth_method',
                'sip_username',
                'sip_password',
                'allowed_ips'
            ]);
        });
    }
};
