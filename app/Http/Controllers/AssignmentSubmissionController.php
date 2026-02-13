<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;

class AssignmentSubmissionController extends Controller
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
    public function create(Course $course,Assignment $assignment)
    {

        Gate::authorize("create",Submission::class);

        $assignment->load('creator');

        return view('course-assignment-submissions.create', compact('course', 'assignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Course $course,Assignment $assignment)
    {

        Gate::authorize("create",Submission::class);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:20000'],
            'files' => ['nullable','array'],
            'files.*' => ['file','max:10240'] // 10MB each
        ]);

        $studentId = auth()->id();

        // Enforce attempt limits (if set)
        if (!is_null($assignment->number_attempts)) {
            $usedAttempts = Submission::query()
                ->where('assignment_id', $assignment->id)
                ->where('student_id', $studentId)
                ->count();

            if ($usedAttempts >= (int) $assignment->number_attempts) {
                return back()
                    ->withErrors(['content' => 'No attempts remaining for this assignment.'])
                    ->withInput();
            }
        }

        $submission = Submission::query()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $studentId,
            'content' => $validated['content'],
            'submitted_at' => now(),
            // attachment_path stays null for now
        ]);

        $disk = config('materials.disk');

        foreach ($request->file('files',[]) as $file){

            $originalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $storedName = Str::uuid() . ($ext ? ".$ext" : '');

            $basePath = "courses/$course->id/assignments/$assignment->id/submissions/$submission->id";

            Storage::disk($disk)->putFileAs(
                $basePath,
                $file,
                $storedName
            );

            $submission->attachments()->create([
                'uploaded_by' => $request->user()->id,
                'original_filename' => $originalName,
                'storage_disk' => $disk,
                'storage_path' => "$basePath/$storedName",
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize()
            ]);
        }

        return redirect()->route('course.assignments.show', [$course, $assignment]);
    }

//    /**
//     * Display the specified resource.
//     */
//    public function show(Submission $submission)
//    {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course,Assignment $assignment,Submission $submission)
    {
        Gate::authorize('update', $submission);

        $this->assertNotGraded($submission);

        $assignment->load('creator');

        $submission_attachments = $submission->attachments();

        return view('course-assignment-submissions.edit', compact('course', 'assignment', 'submission','submission_attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Course $course,Assignment $assignment, Submission $submission)
    {
        Gate::authorize('update', $submission);

        $this->assertNotGraded($submission);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:20000'],
            'files' => ['nullable','array'],
            'files.*' => ['file','max:10240'] // 10MB each
        ]);

        $disk = config('materials.disk');

        foreach ($request->file('files',[]) as $file){

            $originalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $storedName = Str::uuid() . ($ext ? ".$ext" : '');

            $basePath = "courses/$course->id/assignments/$assignment->id/submissions/$submission->id";

            Storage::disk($disk)->putFileAs(
                $basePath,
                $file,
                $storedName
            );

            $submission->attachments()->create([
                'uploaded_by' => $request->user()->id,
                'original_filename' => $originalName,
                'storage_disk' => $disk,
                'storage_path' => "$basePath/$storedName",
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize()
            ]);
        }

        $submission->update([
            'content' => $validated['content'],
            // keep submitted_at as-is (or decide to bump it)
        ]);

        return redirect()->route('course.assignments.show', [$course, $assignment]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Assignment $assignment,Submission $submission)
    {

        Gate::authorize("delete",$submission);

        $this->assertNotGraded($submission);

        $submission->delete();

        return redirect()->route('course.assignments.show', [$course, $assignment]);
    }

    private function assertNotGraded(Submission $submission): void
    {
        if ($submission->isGraded()) {
            abort(403, 'This submission has been graded and can no longer be modified.');
        }
    }

    private function assertSubmissionAttemptsRemain()
    {

    }
}
