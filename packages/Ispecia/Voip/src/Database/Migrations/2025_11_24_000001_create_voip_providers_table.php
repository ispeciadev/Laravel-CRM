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
        Schema::create('voip_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Twilio Production", "Telnyx Sandbox"
            $table->string('driver'); // twilio, telnyx, sip, plivo
            $table->text('config'); // Encrypted JSON with provider-specific configuration
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            // Ensure efficient lookups for active provider
            $table->index(['is_active', 'priority']);
            $table->index('driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voip_providers');
    }
};
