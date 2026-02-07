<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    //

    use HasFactory;

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class,"assignment_id");
    }

    public function submissionsFromUser(int $userId): HasMany
    {
        return $this->hasMany(Submission::class,"assignment_id")
            ->where("submissions.student_id",$userId);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class,"course_id");
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
