<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course,Assignment $assignment)
    {
        //

        if((int)$course->id !== (int) $assignment->course_id) abort(403);

//        $submissions = $assignment->submissionsFromUser(auth()->id());

        $assignment->load('creator');

        $submissions = $assignment->submissionsFromUser(auth()->id())
            ->with('grader')->get();



        return view("course-assignments.show",compact("course","assignment","submissions"));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        //
    }
}
