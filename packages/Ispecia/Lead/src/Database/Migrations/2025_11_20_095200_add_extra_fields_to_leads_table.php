<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('linkedin_url')->nullable();
            $table->string('website')->nullable();
            $table->integer('lead_rating')->nullable();
            $table->integer('employee_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('linkedin_url');
            $table->dropColumn('website');
            $table->dropColumn('lead_rating');
            $table->dropColumn('employee_count');
        });
    }
};
