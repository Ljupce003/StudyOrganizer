<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseMaterialController extends Controller
{
    //
    public function create(Course $course)
    {
        Gate::authorize("create", CourseMaterial::class);

        return view('course-materials.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', CourseMaterial::class);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'], //10MB
            'is_published' => ['sometimes', 'boolean:']
        ]);

        $file = $request->file('file');

        // we can limit based on mime types

        $originalName = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();

        $storedName = Str::uuid() . ($ext ? ".{$ext}" : '');

        $storage_path = "courses/$course->id/materials/$storedName";
        $disk = config('materials.disk');

        Storage::disk($disk)->putFileAs(
            "courses/$course->id/materials",
            $file,
            $storedName);

        CourseMaterial::query()->create([
            'course_id' => $course->id,
            'uploaded_by' => $request->user()->id,
            'title' => $data['title'] ?: pathinfo($originalName, PATHINFO_FILENAME),
            'original_filename' => $originalName,
            'storage_disk' => $disk,
            'storage_path' => $storage_path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'is_published' => (boolean)($data['is_published'] ?? false)
        ]);


        return redirect()
            ->route('course.show', [$course])
            ->with('status', 'Course Material created.');
    }

    public function edit(Course $course, CourseMaterial $material)
    {

        Gate::authorize('update', $material);

        return view('course-materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Course $course, CourseMaterial $material)
    {
        Gate::authorize('update', $material);

        // we can allow file replacement later
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
            'file' => ['nullable', 'file', 'max:20480'], // 10MB example
        ]);

        $material->title = $data['title'];
        $material->is_published = $data['is_published'] ?? false;

        if($request->hasFile('file')){
            $file = $request->file('file');

            $oldPath = $material->storage_path;
//            $disk = $material->storage_disk;
//
            $originalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();

            $storedName = Str::uuid() . ($ext ? ".$ext": '');

            $newPath = "courses/$course->id/materials/$storedName";

            $material->storage()->putFileAs("courses/$course->id/materials",$file,$storedName);
//            Storage::disk($disk)->putFileAs("courses/$course->id/materials",$file,$storedName);

            $material->original_filename = $originalName;
            $material->storage_path = $newPath;
            $material->mime_type = $file->getMimeType();
            $material->size_bytes = $file->getSize();

            $material->save();

            if($oldPath){
                $material->storage()->delete($oldPath);
//                Storage::disk($disk)->delete($oldPath);
            }

            return redirect()
                ->route('course.show', [$course])
                ->with('status', 'Course Material updated and file replaced.');

        }


        $material->save();

        return redirect()
            ->route('course.show', [$course])
            ->with('status', 'Course Material updated.');
    }

    public function destroy(Course $course, CourseMaterial $material)
    {
        Gate::authorize('delete', $material);

        $material->storage()->delete($material->storage_path);
//        Storage::disk($material->disk())->delete($material->storage_path);

        $material->delete();

        return redirect()
            ->route('course.show', [$course])
            ->with('status', 'Course Material deleted.');
    }

    public function fetch(Course $course, CourseMaterial $material)
    {
        Gate::authorize('view', $material);

        return $material->materialResponse();
    }
}
