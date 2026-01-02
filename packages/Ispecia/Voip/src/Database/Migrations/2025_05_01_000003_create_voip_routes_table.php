<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voip_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('did_number')->index(); // The incoming number
            $table->string('action_type'); // 'user', 'queue', 'ivr'
            $table->string('action_target'); // User ID or Queue Name
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voip_routes');
    }
};
