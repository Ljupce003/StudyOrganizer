<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Note;
use App\Models\Submission;
use App\Models\User;
use App\Policies\AssignmentPolicy;
use App\Policies\NotePolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    protected $policies = [
        Note::class => NotePolicy::class,
        Assignment::class => AssignmentPolicy::class,
        Submission::class => SubmissionPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();

        Gate::define("admin",fn(User $user) => $user->hasRole(UserRole::ADMIN));
        Gate::define("professor",fn(User $user) => $user->hasRole(UserRole::PROFESSOR));

        Gate::define('teaching-course',function(User $user, Course $course) {

            return $course->professors()->where("users.id",$user->id)->exists();
        });

        Gate::define('enrolled-in-course', function (User $user,Course $course){

            return $course->students()->where("users.id",$user->id)->exists();
        });

        Gate::define('access-course',function (User $user,Course $course){
            // we can put admin bypass to course here

            if($user->hasRole(UserRole::PROFESSOR)){
                return $course->professors()->where("users.id",$user->id)->exists();
            }

            return $course->students()->where("users.id",$user->id)->exists();
        });


//        Gate::define('user-owns-note',function (User $user, Note $note){
//            return $note->user_id === $user->id;
//        });

    }
}
