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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->constrained("courses")->cascadeOnDelete();
            $table->foreignId("uploaded_by")->constrained("users");
            $table->string("title");
            $table->string("original_filename");
            $table->string("storage_disk")->default("materials");
            $table->string("storage_path");

            $table->string("mime_type")->nullable();
            $table->unsignedBigInteger("size_bytes")->nullable();

            $table->boolean("is_published")->default(true);

            $table->timestamps();

            $table->index(['course_id','created_at']);
            $table->index(['course_id','is_published']);
            $table->unique(['storage_disk','storage_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
