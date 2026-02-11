<?php

namespace App\Http\Controllers;

use App\Enums\GradingStrategy;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseAssignmentController extends Controller
{
//    /**
//     * Display a listing of the resource.
//     */
//    public function index()
//    {
//        //
//    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        //
        Gate::authorize("create", Assignment::class);

        return view('course-assignments.create', compact('course'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Course $course)
    {
        //
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'max_points' => ['required', 'integer', 'min:1', 'max:500'],
            'number_attempts' => ['nullable', 'integer', 'min:1'],
            'due_at' => ['nullable', 'date'],
            'grading_strategy' => ['required', Rule::enum(GradingStrategy::class)],
            'allow_late' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $validated['allow_late'] = $request->boolean('allow_late');
        $validated['is_published'] = $request->boolean('is_published');

        $assignment = Assignment::query()->create([
            'course_id' => $course->id,
            'created_by' => auth()->id(),
            ...$validated,
        ]);

        return redirect()
            ->route('course.assignments.show', [$course, $assignment])
            ->with('success', 'Assignment created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course,Assignment $assignment)
    {

//        if((int)$course->id !== (int) $assignment->course_id) abort(403);

        $editMode = request()->boolean('edit');
        if($editMode){
            Gate::authorize("update", $assignment);
        }

        $assignment->load('creator');

//        $submissions = $assignment->submissionsFromUser(auth()->id())
//            ->with('grader')->get();


        if (auth()->user()->hasRole(UserRole::PROFESSOR)) {
            $isProfessor = true;
            $submissions = Submission::query()
                ->where('assignment_id', $assignment->id)
                ->with([
                    'student:id,name,email',
                    'grader:id,name',
                ])
                ->orderBy('student_id')
                ->orderByDesc('submitted_at')
                ->get()
                ->groupBy('student_id');
        } else {
            $isProfessor = false;
            $submissions = $assignment->submissions()
                ->where('student_id', auth()->id())
                ->with('grader:id,name')
                ->latest('submitted_at')
                ->get();
        }

        return view('course-assignments.show', compact('course','assignment','submissions','editMode','isProfessor'));


//        return view("course-assignments.show",compact("course","assignment","submissions"));

    }

//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(Assignment $assignment)
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Course $course, Assignment $assignment)
    {
        //
        Gate::authorize("update", $assignment);

        if ($request->has('course_id') || $request->has('created_by')) {
            abort(403, 'Forbidden field modification attempt');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'max_points' => ['required', 'integer', 'min:1', 'max:500'],
            'number_attempts' => ['nullable', 'integer', 'min:1'],
            'due_at' => ['nullable', 'date'],
            'grading_strategy' => ['required', Rule::enum(GradingStrategy::class)],
            'allow_late' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $validated['allow_late'] = $request->boolean('allow_late');
        $validated['is_published'] = $request->boolean('is_published');

        $assignment->update($validated);

        return redirect()
            ->route('course.assignments.show', [$assignment->course_id, $assignment])
            ->with('success', 'Assignment updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course,Assignment $assignment)
    {
//        if((int) $assignment->course_id !== $course) abort(404);

        Gate::authorize("delete", $assignment);

        $assignment->delete();

        return redirect()->route('course.show',[$course]);

    }
}
