<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    //

    use HasFactory;

    protected $guarded = [
        'id',
        'created_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class,"assignment_id");
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class,"graded_by");
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class,"student_id");
    }

    function isGraded(): bool
    {
        return $this->grade !== null
            || $this->feedback !== null
            || $this->graded_at !== null
            || $this->graded_by !== null;
    }
}
