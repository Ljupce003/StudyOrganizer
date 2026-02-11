<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
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
    public function create()
    {
        //
        return view("notes.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $note = Note::query()->create([
            'user_id' => auth()->id(),
            'course_id' => null, // global note for now
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
        ]);

        return redirect()->route('notes.edit', $note);
    }

//    /**
//     * Display the specified resource.
//     */
//    public function show(Note $note)
//    {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {

        Gate::authorize('update', $note);

//        if ($note->user_id !== auth()->id() || $note->course_id !== null) abort(404);
        return view('notes.edit', compact('note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {

        Gate::authorize('update', $note);
        //
//        if ($note->user_id !== auth()->id() || $note->course_id !== null) abort(404);

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
    public function destroy(Note $note)
    {
//        if ($note->user_id !== auth()->id() || $note->course_id !== null) abort(404);


        Gate::authorize('update', $note);

        $note->delete();

        return redirect()->route('dashboard');
    }
}
