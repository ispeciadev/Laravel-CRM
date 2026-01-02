<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voip_calls', function (Blueprint $table) {
            $table->id();
            $table->string('sid')->unique()->nullable(); // Provider specific ID
            $table->string('direction'); // inbound, outbound
            $table->string('status')->default('initiated'); // initiated, ringing, in-progress, completed, failed, busy, no-answer
            
            $table->string('from_number');
            $table->string('to_number');
            
            $table->unsignedInteger('user_id')->nullable(); // The user who made/received the call
            $table->unsignedInteger('lead_id')->nullable();
            $table->unsignedInteger('person_id')->nullable();
            $table->unsignedBigInteger('deal_id')->nullable();
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->default(0); // in seconds
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('voip_calls');
    }
};
