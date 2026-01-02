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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('deal_value', 12, 4)->default(0);
            $table->string('status')->default('open');
            $table->string('lost_reason')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->dateTime('closed_at')->nullable();
            
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('person_id')->nullable();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');

            $table->unsignedInteger('lead_id')->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');

            $table->unsignedInteger('lead_source_id')->nullable();
            $table->foreign('lead_source_id')->references('id')->on('lead_sources')->onDelete('set null');

            $table->unsignedInteger('lead_type_id')->nullable();
            $table->foreign('lead_type_id')->references('id')->on('lead_types')->onDelete('set null');

            $table->unsignedInteger('lead_pipeline_id')->nullable();
            $table->foreign('lead_pipeline_id')->references('id')->on('lead_pipelines')->onDelete('set null');

            $table->unsignedInteger('lead_pipeline_stage_id')->nullable();
            $table->foreign('lead_pipeline_stage_id')->references('id')->on('lead_pipeline_stages')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
