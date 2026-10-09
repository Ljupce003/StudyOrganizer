<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Course;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(DiagnosingHealth::class, function (): void {
            // /up must fail when the database is unavailable, too.
            DB::select('SELECT 1');
        });
        View::composer('*', function ($view) {
            $user = auth()->user();

            $sidebarCourses = collect();
            $sidebarGlobalNotes = collect();
            $sidebarCourseNotes = collect();
            $currentCourse = null;

            if ($user) {
                if($user->hasRole(UserRole::ADMIN)){
                    $sidebarCourses = Course::query()
                        ->select('courses.id', 'courses.short_name','courses.name','courses.code')
                        ->orderBy('year',"desc")
                        ->orderBy('short_name')
                        ->get();
                }
                elseif($user->hasRole(UserRole::PROFESSOR)){
                    $sidebarCourses = $user->teachingCourses()
                        ->select('courses.id', 'courses.short_name','courses.name','courses.code')
                        ->orderBy('year',"desc")
                        ->orderBy('short_name')
                        ->get();
                }
                else{
                    $sidebarCourses = $user->courses()
                        ->select('courses.id', 'courses.short_name','courses.name','courses.code')
                        ->orderBy('year',"desc")
                        ->orderBy('short_name')
                        ->get();
                }


                // Global notes only (course_id null)
                $sidebarGlobalNotes = $user->notes()
                    ->whereNull('course_id')
                    ->select('id', 'title', 'updated_at')
                    ->orderByDesc('updated_at')
                    ->limit(8)
                    ->get();

                // If we're inside a course route, load course notes
                $courseParam = request()->route('course'); // could be Course model or id or null
                if ($courseParam) {
                    $currentCourse = $courseParam instanceof Course
                        ? $courseParam
                        : Course::query()->find($courseParam);

                    if ($currentCourse) {
                        $sidebarCourseNotes = $user->notes($currentCourse->id)
                            ->where('course_id', $currentCourse->id)
                            ->select('id', 'title', 'updated_at')
                            ->orderByDesc('updated_at')
                            ->limit(8)
                            ->get();
                    }
                }
            }

            $view->with([
                'sidebarCourses' => $sidebarCourses,
                'sidebarGlobalNotes' => $sidebarGlobalNotes,
                'sidebarCourseNotes' => $sidebarCourseNotes,
                'currentCourse' => $currentCourse,
            ]);
        });
    }

}
