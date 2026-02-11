<?php

use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseNoteController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionGradingController;
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


    //notes
    Route::prefix('notes')->name('notes.')->group(function (){
        Route::get('/create', [NoteController::class, 'create'])->name('create');
        Route::post('/', [NoteController::class, 'store'])->name('store');
        Route::get('/{note}/edit', [NoteController::class, 'edit'])->name('edit');
        Route::put('/{note}', [NoteController::class, 'update'])->name('update');
        Route::delete('/{note}', [NoteController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('courses/{course}')
        ->name('course.')
        ->middleware('can:access-course,course')   // authorize course access once
        ->scopeBindings()                          // URL integrity for nested models
        ->group(function () {

            // only to show a specific course, the rest of CRUD is in admin panel
            Route::get('/', [CourseController::class, 'show'])->name('show');

            // Course notes
            Route::prefix('notes')->name('notes.')->group(function () {

                Route::get('/create', [CourseNoteController::class, 'create'])->name('create');
                Route::post('/', [CourseNoteController::class, 'store'])->name('store');
                Route::get('/{note}/edit', [CourseNoteController::class, 'edit'])->name('edit');
                Route::put('/{note}', [CourseNoteController::class, 'update'])->name('update');
                Route::delete('/{note}', [CourseNoteController::class, 'destroy'])->name('destroy');
            });

            // Assignments
            Route::prefix('assignments')->name('assignments.')->group(function () {
                Route::get('/create', [CourseAssignmentController::class, 'create'])->name('create');
                Route::post('/', [CourseAssignmentController::class, 'store'])->name('store');
                Route::get('/{assignment}', [CourseAssignmentController::class, 'show'])->name('show');
//                Route::get('/{assignment}/edit', [CourseAssignmentController::class, 'edit'])->name('edit');
                Route::put('/{assignment}', [CourseAssignmentController::class, 'update'])->name('update');
                Route::delete('/{assignment}', [CourseAssignmentController::class, 'destroy'])->name('destroy');

                // Submissions nested under assignment (if you do it this way)
                Route::prefix('{assignment}/submissions')->name('submissions.')->group(function () {
                    Route::get('/create', [AssignmentSubmissionController::class, 'create'])->name('create');
                    Route::post('/', [AssignmentSubmissionController::class, 'store'])->name('store');
                    Route::get('/{submission}/edit', [AssignmentSubmissionController::class, 'edit'])->name('edit');
                    Route::put('/{submission}', [AssignmentSubmissionController::class, 'update'])->name('update');
                    Route::delete('/{submission}', [AssignmentSubmissionController::class, 'destroy'])->name('destroy');
                });

                // Grading routes (explicit, not CRUD)
                Route::get('/{assignment}/submissions/{submission}/grade', [SubmissionGradingController::class, 'edit'])
                    ->name('submissions.grade.edit');

                Route::put('/{assignment}/submissions/{submission}/grade', [SubmissionGradingController::class, 'update'])
                    ->name('submissions.grade.update');

                Route::delete('/{assignment}/submissions/{submission}/grade',
                    [SubmissionGradingController::class, 'destroy']
                )->name('submissions.grade.destroy');
            });


        });

//    Route::resource("courses", CourseController::class);

//    Route::resource("notes", NoteController::class)->except(['index', 'show']);
//    Route::resource("courses.notes", CourseNoteController::class)->except(['index', 'show'])->scoped();

//    Route::resource("course.assignments", CourseAssignmentController::class)->scoped();
//    Route::resource("course.assignments.submissions", AssignmentSubmissionController::class);


//    Route::get(
//        'course/{course}/assignments/{assignment}/submissions/{submission}/grade',
//        [SubmissionGradingController::class, 'edit']
//    )->name('course.assignments.submissions.grade.edit');
//
//    Route::put(
//        'course/{course}/assignments/{assignment}/submissions/{submission}/grade',
//        [SubmissionGradingController::class, 'update']
//    )->name('course.assignments.submissions.grade.update');

});

require __DIR__.'/auth.php';
