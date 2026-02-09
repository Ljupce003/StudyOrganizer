@php use App\Enums\GradingStrategy; @endphp
<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            {{-- Course short name above title --}}
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $assignment->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $isEdit = $editMode ?? false;
            @endphp

            @if($isEdit)
                @include('course-assignments.components.details-edit',[
                        'course' => $course,
                        'assignment' => $assignment,])
            @else
                {{-- Assignment details card --}}
                @include('course-assignments.components.details-read', [
                        'course' => $course,
                        'assignment' => $assignment,])
            @endif


            {{-- Submissions section --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Your submissions
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Submissions you’ve made for this assignment.
                    </p>
                </div>

                @if($isProfessor)
                    @include('course-assignment-submissions.components.professor-list', [
                        'course' => $course,
                        'assignment' => $assignment,
                        'submissions' => $submissions,
                    ])
                @else
                    @include('course-assignment-submissions.components.student-list', [
                        'course' => $course,
                        'assignment' => $assignment,
                        'submissions' => $submissions,
                    ])
                @endif

            </div>

        </div>
    </div>
</x-layouts.app-with-sidebar>
