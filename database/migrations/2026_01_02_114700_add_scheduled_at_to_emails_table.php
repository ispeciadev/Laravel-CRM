<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            if (! Schema::hasColumn('emails', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            if (Schema::hasColumn('emails', 'scheduled_at')) {
                $table->dropIndex(['scheduled_at']);
                $table->dropColumn('scheduled_at');
            }
        });
    }
};
