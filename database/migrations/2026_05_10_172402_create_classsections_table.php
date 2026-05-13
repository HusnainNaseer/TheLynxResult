<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();

            // Local FK → classes.id
            $table->unsignedBigInteger('class_id')->nullable()->index();

            // Local FK → sections.id
            $table->unsignedBigInteger('section_id')->nullable()->index();

            // ERP mirror IDs
            $table->string('erp_class_id')->nullable()->index();
            $table->string('erp_section_id')->nullable()->index();

            $table->timestamps();

            // No duplicate class-section pairs
            $table->unique(['class_id', 'section_id']);

            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->nullOnDelete();

            $table->foreign('section_id')
                  ->references('id')->on('sections')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};