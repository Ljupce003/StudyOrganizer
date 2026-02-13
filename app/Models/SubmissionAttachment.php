<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionAttachment extends Model
{
    //


    protected $fillable = [
        'submission_id',
        'uploaded_by',
        'original_filename',
        'storage_disk',
        'storage_path',
        'mime_type',
        'size_bytes',
    ];

    protected $casts = [
        'size_bytes' => 'integer'
    ];


    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class,"submission_id");
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class,"uploaded_by");
    }


    public function disk(): string
    {
        return $this->storage_disk ?: config('materials.disk');
    }

    public function storage(): Filesystem
    {
        return Storage::disk($this->disk());
    }

    public function inlineResponse(): StreamedResponse
    {
        return $this->storage()->response(
            $this->storage_path,
            $this->original_filename,
            [
                'Content-Disposition' => 'inline; filename="'. $this->original_filename . '"'
            ]
        );
    }


    public function downloadResponse(): StreamedResponse
    {
        return $this->storage()->download(
            $this->storage_path,
            $this->original_filename
        );
    }

    public function attachmentResponse(): StreamedResponse
    {
        if(str_starts_with($this->mime_type,"image/") ||
            $this->mime_type === 'application/pdf'){
            return $this->inlineResponse();
        }
        return $this->downloadResponse();
    }

    public static function typeLabel($file_model): string
    {
        $mime = $file_model->mime_type ?? '';
        $extension = strtolower(pathinfo($file_model->original_filename ?? '', PATHINFO_EXTENSION));

        return match (true) {

            // ---- Documents ----
            str_contains($mime, 'pdf') => 'PDF',
            in_array($extension, ['doc', 'docx']) => 'Word',
            in_array($extension, ['xls', 'xlsx']) => 'Excel',
            in_array($extension, ['ppt', 'pptx']) => 'PowerPoint',
            $extension == 'txt' => 'Text',

            // ---- Images ----
            str_starts_with($mime, 'image/') => 'Image',
            $extension == 'svg' => 'SVG',

            // ---- Archives ----
            str_contains($mime, 'zip') => 'ZIP',
            $extension == 'rar' => 'RAR',
            $extension == '7z' => '7z',

            // ---- Code ----
            in_array($extension, ['php', 'js', 'ts', 'py', 'java', 'cs', 'cpp', 'c', 'html', 'css']) => 'Code',

            // ---- Markdown ----
            $extension == 'md' => 'Markdown',

            // ---- Fallback ----
            default => strtoupper($extension ?: 'File'),
        };
    }



}
