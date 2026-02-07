<?php

use App\Enums\GradingStrategy;
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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->constrained("courses");
            $table->foreignId("created_by")->constrained("users");
            $table->string("title");
            $table->longText("description");

            $table->unsignedSmallInteger("max_points");
            $table->unsignedSmallInteger("number_attempts")->nullable();
            $table->string("grading_strategy")->default(GradingStrategy::FIRST->value);
            $table->dateTime("due_at")->nullable();
            $table->boolean("allow_late")->default(false);

            $table->boolean("is_published")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
