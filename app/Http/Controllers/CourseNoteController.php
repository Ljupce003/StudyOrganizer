<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Note;
use Illuminate\Http\Request;

class CourseNoteController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        // Later: policy/authorization (enrolled, teacher, etc.)
        return view('course-notes.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Course $course)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $note = Note::query()->create([
            'user_id' => auth()->id(),
            'course_id' => $course->id, // COURSE
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
        ]);

        return redirect()->route('courses.notes.edit', [$course, $note]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Note $note)
    {
        if ($note->user_id !== auth()->id()) abort(404);
        if ((int) $note->course_id !== (int) $course->id) abort(404);

        return view('course-notes.edit', compact('course', 'note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Course $course, Note $note)
    {
        if ($note->user_id !== auth()->id()) abort(404);
        if ((int) $note->course_id !== (int) $course->id) abort(404);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $note->update($data);

        return back()->with('status', 'Saved.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Note $note)
    {
        if ($note->user_id !== auth()->id()) abort(404);
        if ((int) $note->course_id !== (int) $course->id) abort(404);

        $note->delete();

        return redirect()->route('courses.show', $course);
    }
}
