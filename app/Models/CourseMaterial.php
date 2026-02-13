<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMaterial extends Model
{
    //

    protected $fillable = [
        'course_id',
        'uploaded_by',
        'title',
        'original_filename',
        'storage_disk',
        'storage_path',
        'mime_type',
        'size_bytes',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'size_bytes' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class,"course_id");
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class,"uploaded_by");
    }


    public function disk()
    {
        return $this->storage_disk ?: config('materials.disk');
    }

    public function storage(): Filesystem
    {
        return Storage::disk($this->disk());
    }

    function existsInStorage(): bool
    {
        return $this->storage()->exists($this->storage_path);
    }

    function downloadResponse(?string $downloadName = null): StreamedResponse
    {
        $name = $downloadName
            ?? $this->original_filename
            ?? basename($this->storage_path);

        return $this->storage()->download($this->storage_path,$name);
    }

    function inlineResponse(?string $downloadName = null): StreamedResponse
    {
        $name = $downloadName
            ?? $this->original_filename
            ?? basename($this->storage_path);

        return $this->storage()->response(
            $this->storage_path,
            $name,[
                'Content-Disposition' => 'inline; filename="'.$name.'"'
        ]);
    }

    function materialResponse(?string $downloadName = null): StreamedResponse
    {

        if(str_starts_with($this->mime_type,"image/") ||
         $this->mime_type === 'application/pdf'){
            return $this->inlineResponse($downloadName);
        }

        return $this->downloadResponse($downloadName);
    }

}
