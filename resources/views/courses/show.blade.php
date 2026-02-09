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
                    @if(true)
                        <a
                            href="{{ route("course.assignments.create",[$course]) }}"
                            type="button"
                            class="inline-flex items-center px-3 py-2 bg-black text-white rounded-md text-sm font-medium hover:bg-gray-700 opacity-70 cursor-pointer"
                            title="Coming soon"
                        >
                            + New
                        </a>
                    @endif

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
                                    <div class="flex items-start justify-between">
                                        <div class="space-y-1">
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $assignment->title }}
                                            </h4>

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

                    {{-- Later show only to professors/admin --}}
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700 opacity-50 cursor-not-allowed"
                        disabled
                        title="File uploads later"
                    >
                        Upload
                    </button>
                </div>

                <div class="p-6">
                    <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                        <p class="text-gray-600 font-medium">No materials uploaded</p>
                        <p class="text-sm text-gray-500 mt-1">
                            PDFs, slides, and links for this course will live here later.
                        </p>
                    </div>
                </div>
            </section>

        </div>
    </div>
</x-layouts.app-with-sidebar>
{{--</x-app-layout>--}}
