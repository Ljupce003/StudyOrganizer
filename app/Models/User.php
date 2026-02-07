<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function Courses(): BelongsToMany
    {
        return $this
            ->belongsToMany(Course::class,"course_user","user_id","course_id")
            ->withPivot(["status","enrolled_at"]);
    }

    public function teachingCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class,"course_professor","user_id","course_id");
    }

    public function notes(?int $courseId = null): User|HasMany
    {
        return $this->hasMany(Note::class,"user_id")
            ->where("notes.course_id",$courseId);
    }
}
