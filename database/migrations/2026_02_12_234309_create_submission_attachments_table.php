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
        Schema::create('submission_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId("submission_id")->constrained('submissions')->cascadeOnDelete();
            $table->foreignId("uploaded_by")->constrained("users")->cascadeOnDelete();

            $table->string("original_filename");
            $table->string("storage_disk")->nullable();
            $table->string("storage_path");
            $table->string("mime_type")->nullable();
            $table->unsignedBigInteger("size_bytes")->nullable();

            $table->timestamps();

            $table->index(['submission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_attachments');
    }
};
