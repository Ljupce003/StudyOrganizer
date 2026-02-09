<?php

use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseNoteController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionGradingController;
use App\Livewire\Notes\CreateCourseNote;
use App\Livewire\Notes\CreateGlobalNote;
use App\Livewire\Notes\EditCourseNote;
use App\Livewire\Notes\EditGlobalNote;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource("courses", CourseController::class);

    Route::resource("notes", NoteController::class)->except(['index', 'show']);
    Route::resource("courses.notes", CourseNoteController::class)->except(['index', 'show']);

    Route::resource("course.assignments", CourseAssignmentController::class);
    Route::resource("course.assignments.submissions", AssignmentSubmissionController::class);


    Route::get(
        'course/{course}/assignments/{assignment}/submissions/{submission}/grade',
        [SubmissionGradingController::class, 'edit']
    )->name('course.assignments.submissions.grade.edit');

    Route::put(
        'course/{course}/assignments/{assignment}/submissions/{submission}/grade',
        [SubmissionGradingController::class, 'update']
    )->name('course.assignments.submissions.grade.update');

});

require __DIR__.'/auth.php';
