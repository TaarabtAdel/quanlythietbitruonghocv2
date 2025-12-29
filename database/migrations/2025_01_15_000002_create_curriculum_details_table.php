<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->string('subject_name');
            $table->integer('credits')->default(0);
            $table->integer('hours')->default(0);
            $table->integer('semester')->nullable();
            $table->integer('order')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('curriculum_id')->references('id')->on('curricula')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_details');
    }
};

