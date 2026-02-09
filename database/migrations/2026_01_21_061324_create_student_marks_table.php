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
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->integer('result_id');
            $table->integer('subject_id');
            $table->decimal('term_one_mark',5,2)->nullable();
            $table->string('term_one_grade')->nullable();
            $table->decimal('term_one_percent',5,2)->nullable();
            $table->decimal('term_one_total',5,2)->nullable();
            $table->decimal('term_two_mark',5,2)->nullable();
            $table->string('term_two_grade')->nullable();
            $table->decimal('term_two_percent',5,2)->nullable();
            $table->decimal('term_two_total',5,2)->nullable();
            $table->decimal('grand_term_one',5,2)->nullable();
            $table->decimal('grand_term_two',5,2)->nullable();
            $table->integer('w_days_term_one')->description('working days term one')->nullable();
            $table->integer('w_days_term_two')->description('working days term two')->nullable();
            $table->integer('w_days_total')->nullable();
            $table->text('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};
