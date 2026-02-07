<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        "code",
        "name",
        "short_name",
        "year",
        "semester",
        "is_active",
    ];

    public function students(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class,"course_user","course_id","user_id")
            ->withPivot(["status","enrolled_at"])
            ->where("users.role",UserRole::STUDENT);
    }

    public function professors(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class,"course_professor","course_id","user_id")
            ->where("users.role",UserRole::PROFESSOR);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class,"course_id");
    }


}
