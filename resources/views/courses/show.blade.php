@php use App\Enums\UserRole;use App\Models\Assignment;use App\Models\CourseMaterial;use App\Models\SubmissionAttachment; @endphp
{{--<x-app-layout>--}}
<x-layouts.app-with-sidebar :course="$course">
    {{--        <x-slot name="header">--}}
    {{--            <div class="flex flex-col gap-1">--}}
    {{--                <h2 class="font-semibold text-xl text-gray-800 leading-tight">--}}
    {{--                    {{ $course->code }} — {{ $course->name }}--}}
    {{--                </h2>--}}
    {{--                @if($course->short_name)--}}
    {{--                    <p class="text-sm text-gray-500">{{ $course->short_name }}</p>--}}
    {{--                @endif--}}
    {{--            </div>--}}
    {{--        </x-slot>--}}

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-4">

                <div class="flex flex-col gap-1 ">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $course->code }} — {{ $course->name }}
                    </h2>
                    @if($course->short_name)
                        <p class="text-sm text-gray-500">{{ $course->short_name }}</p>
                    @endif
                </div>

                {{-- Quick actions --}}
                <div class=" pt-4 flex flex-wrap gap-3">

                    {{-- Placeholder buttons (later real routes) --}}
                    <a
                        href="#assignments"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Assignments
                    </a>
                    <a
                        href="#materials"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Materials
                    </a>
                </div>
            </div>


            {{-- Assignments section --}}
            <section id="assignments" class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Assignments</h3>

                    {{-- Later show only to professors/admin --}}
                    @can('create',Assignment::class)
                        <a
                            href="{{ route("course.assignments.create",[$course]) }}"
                            type="button"
                            class="inline-flex items-center px-3 py-2 bg-black text-white rounded-md text-sm font-medium hover:bg-gray-700 opacity-70 cursor-pointer"
                            title="Coming soon"
                        >
                            + New
                        </a>
                    @endcan

                </div>

                @if($course_assignments->isNotEmpty())
                    <div class="flex flex-col items-center px-2 py-4 gap-2" style="">
                        @foreach($course_assignments as $assignment)
                            <a
                                href="{{ route('course.assignments.show', [$course, $assignment]) }}"
                                class="w-full max-w-5xl block"
                            >
                                <div class="bg-white border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition">

                                    {{-- Top row --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                                        <div class="space-y-1">

                                            <div class="flex items-start gap-2">
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ $assignment->title }}
                                                </h4>

                                                @if(auth()->user()->hasRole(UserRole::PROFESSOR) || auth()->user()->hasRole(UserRole::ADMIN))
                                                    @if(!$assignment->is_published)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                       bg-yellow-100 text-yellow-900 border border-yellow-300"
                                                            title="Students can't see this yet"
                                                        >
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                Unpublished
            </span>
                                                    @endif
                                                @endif
                                            </div>


                                            <div class="text-sm text-gray-500">
                                                Created by
                                                <span
                                                    class="text-gray-700 font-medium">{{ $assignment->creator->name ?? 'Unknown' }}</span>
                                                ·
                                                <span>{{ $assignment->created_at->format('Y-m-d') }}</span>
                                            </div>
                                        </div>

                                        {{-- Due date --}}
                                        <div class="text-right">
                                            @if($assignment->due_at)
                                                <div class="text-sm text-gray-500">Due</div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $assignment->due_at->format('Y-m-d H:i') }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-400">
                                                    No due date
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                            </a>
                        @endforeach
                    </div>

                @else
                    <div class="p-6">
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                            <p class="text-gray-600 font-medium">No assignments yet</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Once you add assignments, they’ll show up here with due dates and status.
                            </p>
                        </div>
                    </div>
                @endif


            </section>

            {{-- Materials section --}}
            <section id="materials" class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Materials</h3>

                    @can('create', [CourseMaterial::class, $course])
                        <a
                            href="{{ route('course.materials.create', [$course]) }}"
                            class="inline-flex items-center px-3 py-2 bg-black text-white rounded-md text-sm font-medium hover:bg-gray-700 opacity-70 cursor-pointer"
                        >
                            + Upload
                        </a>
                    @endcan
                </div>

                @if(isset($course_materials) && $course_materials->isNotEmpty())
                    <div class="p-4">
                        <div class="flex flex-col gap-3">
                            @foreach($course_materials as $material)
                                @php
                                    $isStaff = !auth()->user()->hasRole(UserRole::STUDENT); // adjust if needed
                                    $fetchUrl = route('course.materials.fetch', [$course, $material,$material->original_filename]);

                                    $typeLabel = null;
                                    if ($material->mime_type) {
                                        $typeLabel = SubmissionAttachment::typeLabel($material);

                                    }
                                @endphp

                                @if(!$isStaff)
                                    {{-- STUDENT: whole card is a link --}}
                                    <a href="{{ $fetchUrl }}"
                                       class="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                        <div class="min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 truncate">
                                                {{ $material->title }}
                                            </h4>

                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                    <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                        <span class="text-gray-500">By</span>
                        <span class="font-medium text-gray-800">{{ $material->uploader->name ?? 'Unknown' }}</span>
                    </span>

                                                <span
                                                    class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                        <span class="text-gray-500">Uploaded</span>
                        <span class="font-medium text-gray-800">{{ $material->created_at->format('Y-m-d') }}</span>
                    </span>

                                                @if($material->size_bytes)
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                            <span class="text-gray-500">Size</span>
                            <span class="font-medium text-gray-800">{{ number_format($material->size_bytes / 1024, 1) }} KB</span>
                        </span>
                                                @endif

                                                @if($typeLabel)
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                            <span class="text-gray-500">Type</span>
                            <span class="font-medium text-gray-800">{{ $typeLabel }}</span>
                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    {{-- STAFF: left side clickable, right side actions --}}
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                                            {{-- Clickable content area --}}
                                            <a href="{{ $fetchUrl }}" class="block min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="text-base font-semibold text-gray-900 truncate">
                                                        {{ $material->title }}
                                                    </h4>

                                                    @if(!$material->is_published)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                       bg-yellow-50 text-yellow-900 border border-yellow-200"
                                                            title="Students can't see this yet"
                                                        >
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                Unpublished
                            </span>
                                                    @endif
                                                </div>

                                                <div
                                                    class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                            <span class="text-gray-500">By</span>
                            <span class="font-medium text-gray-800">{{ $material->uploader->name ?? 'Unknown' }}</span>
                        </span>

                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                            <span class="text-gray-500">Uploaded</span>
                            <span class="font-medium text-gray-800">{{ $material->created_at->format('Y-m-d') }}</span>
                        </span>

                                                    @if($material->size_bytes)
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                                <span class="text-gray-500">Size</span>
                                <span class="font-medium text-gray-800">{{ number_format($material->size_bytes / 1024, 1) }} KB</span>
                            </span>
                                                    @endif

                                                    @if($typeLabel)
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1">
                                <span class="text-gray-500">Type</span>
                                <span class="font-medium text-gray-800">{{ $typeLabel }}</span>
                            </span>
                                                    @endif
                                                </div>
                                            </a>

                                            {{-- Actions (not clickable area) --}}
                                            <div class="flex flex-wrap items-center gap-2 shrink-0 justify-end">
                                                @can('update', $material)
                                                    <a
                                                        href="{{ route('course.materials.edit', [$course, $material]) }}"
                                                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                    >
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('delete', $material)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('course.materials.destroy', [$course, $material]) }}"
                                                        onsubmit="return confirm('Delete this material? This will remove the file too.');"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50"
                                                        >
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach


                        </div>
                    </div>
                @else
                    <div class="p-6">
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                            <p class="text-gray-600 font-medium">No materials uploaded</p>
                            <p class="text-sm text-gray-500 mt-1">
                                PDFs, slides, and other files for this course will show up here.
                            </p>

                            @can('create', [CourseMaterial::class, $course])
                                <div class="mt-4">
                                    <a
                                        href="{{ route('course.materials.create', [$course]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
                                    >
                                        Upload first material
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endif
            </section>


        </div>
    </div>
</x-layouts.app-with-sidebar>
{{--</x-app-layout>--}}
