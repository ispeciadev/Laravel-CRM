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
            $table->foreignId('voip_provider_id')
                ->nullable()
                ->after('id')
                ->constrained('voip_providers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voip_trunks', function (Blueprint $table) {
            $table->dropForeign(['voip_provider_id']);
            $table->dropColumn('voip_provider_id');
        });
    }
};
