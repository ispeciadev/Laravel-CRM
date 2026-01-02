<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voip_recordings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_id');
            $table->string('path'); // path in storage or external URL
            $table->string('disk')->default('local'); // local, s3, etc.
            $table->string('format')->default('mp3');
            $table->integer('duration')->default(0);
            $table->timestamps();
            
            $table->foreign('call_id')->references('id')->on('voip_calls')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('voip_recordings');
    }
};
