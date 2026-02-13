{{--<x-app-layout>--}}
    <x-layouts.app-with-sidebar>
        <div class="rounded-lg bg-white shadow p-6">
            <h1 class="text-xl font-semibold">Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back.</p>
        </div>


        <section class="bg-white shadow rounded-lg mt-6">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Your courses</h3>
            </div>

            @if($sidebarCourses->isNotEmpty())
                <div class="p-4">
                    <div class="flex flex-col gap-3">
                        @foreach($sidebarCourses as $course)
                            <a
                                href="{{ route('course.show', $course) }}"
                                class="block border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition"
                            >
                                <div class="flex flex-col gap-1">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $course->code }} — {{ $course->name }}
                                    </h4>

                                    @if($course->short_name)
                                        <p class="text-sm text-gray-500">
                                            {{ $course->short_name }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-6">
                    <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                        <p class="text-gray-600 font-medium">No courses assigned</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Once you’re enrolled in a course, it will appear here.
                        </p>
                    </div>
                </div>
            @endif
        </section>


    </x-layouts.app-with-sidebar>
{{--</x-app-layout>--}}
