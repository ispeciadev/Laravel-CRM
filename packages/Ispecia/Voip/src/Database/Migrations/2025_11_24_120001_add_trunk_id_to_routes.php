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
        Schema::table('voip_routes', function (Blueprint $table) {
            $table->foreignId('voip_trunk_id')
                ->nullable()
                ->after('id')
                ->constrained('voip_trunks')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voip_routes', function (Blueprint $table) {
            $table->dropForeign(['voip_trunk_id']);
            $table->dropColumn('voip_trunk_id');
        });
    }
};
