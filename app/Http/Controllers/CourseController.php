<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
//    /**
//     * Display a listing of the resource.
//     */
//    public function index()
//    {
//        //
//    }
//
//    /**
//     * Show the form for creating a new resource.
//     */
//    public function create()
//    {
//        //
//    }
//
//    /**
//     * Store a newly created resource in storage.
//     */
//    public function store(Request $request)
//    {
//        //
//    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //

        if(auth()->user()->hasRole(UserRole::STUDENT)){
            $course_assignments = Assignment::query()
                ->where('course_id', $course->id)
                ->where("is_published",true)
                ->with('creator:id,name,email')
                ->latest()
                ->get();

            $course_materials = $course->materials()->where("is_published",true)->oldest()->get();
        }else {
            $course_assignments = Assignment::query()
                ->where('course_id', $course->id)
                ->with('creator:id,name,email')
                ->latest()
                ->get();

            $course_materials = $course->materials()->oldest()->get();
        }





        return view("courses.show",compact("course","course_assignments",'course_materials'));
    }

//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(string $id)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     */
//    public function update(Request $request, string $id)
//    {
//        //
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     */
//    public function destroy(string $id)
//    {
//        //
//    }
}
