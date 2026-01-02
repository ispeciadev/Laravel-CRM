<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voip_trunks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider')->default('custom'); // twilio, custom, asterisk
            $table->string('host')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable(); // Encrypted
            $table->integer('port')->default(5060);
            $table->string('transport')->default('udp'); // udp, tcp, tls
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voip_trunks');
    }
};
