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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("assignment_id")->constrained("assignments")->cascadeOnDelete();
            $table->foreignId("student_id")->constrained("users")->nullOnDelete();

            $table->longText("content")->nullable(); //text answer or link

            $table->dateTime("submitted_at")->nullable();

            $table->unsignedSmallInteger("grade")->nullable();
            $table->longText("feedback")->nullable();

            $table->foreignId("graded_by")->nullable()->constrained("users")->nullOnDelete();

            $table->dateTime("graded_at")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
