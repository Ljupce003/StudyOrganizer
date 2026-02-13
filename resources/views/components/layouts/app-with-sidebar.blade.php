<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>

            <aside class="w-auto lg:flex-none lg:w-72 shrink-0">
                <div class="lg:sticky lg:top-6 space-y-6">
                    <!-- Courses card -->
                    <div class="rounded-lg bg-white shadow p-4 overflow-hidden">
                        <div class="text-sm font-semibold text-gray-700 mb-3">My Courses</div>
                        <nav class="space-y-2 min-w-0">
                            @forelse($sidebarCourses as $course)
                                @php
                                    $isActiveCourse = $currentCourse && $currentCourse->id === $course->id;
                                @endphp

                                <a href="{{ route('course.show', $course) }}"
                                   class="block rounded px-3 py-2 text-sm truncate transition
                                            {{ $isActiveCourse
                                                ? 'bg-gray-200 text-gray-900 font-medium'
                                                : 'hover:bg-gray-100 text-gray-700'
                                            }}">
                                    {{ $course->short_name }}
                                </a>

                                {{--                                <a href="{{ route('course.show', $course) }}"--}}
{{--                                   class="block rounded px-3 py-2 text-sm hover:bg-gray-100 truncate">--}}
{{--                                    {{ $course->short_name }}--}}
{{--                                </a>--}}
                            @empty
                                <div class="text-sm text-gray-500">No enrolled courses.</div>
                            @endforelse
                        </nav>
                    </div>

                    <!-- Course Notes card -->
                    @if($currentCourse)
                        <div class="rounded-lg bg-white shadow p-4 overflow-hidden">
                            <div class="flex items-center justify-between mb-3 min-w-0">
                                <div class="text-sm font-semibold text-gray-700 truncate">
                                    Course Notes
                                </div>

                                <a href="{{ route('course.notes.create', $currentCourse) }}"
                                   class="text-xs text-blue-600 hover:underline shrink-0">
                                    New
                                </a>
                            </div>

                            <nav class="space-y-2 min-w-0">
                                @forelse($sidebarCourseNotes as $note)
                                    @php
                                        $isActiveCourseNote = request()->routeIs('course.notes.edit')
                                            && request()->route('note')?->id === $note->id;
                                    @endphp

                                    <a href="{{ route('course.notes.edit', [$currentCourse, $note]) }}"
                                       class="block rounded px-3 py-2 text-sm truncate transition
                                                {{ $isActiveCourseNote
                                                    ? 'bg-gray-200 text-gray-900 font-medium'
                                                    : 'hover:bg-gray-100 text-gray-700'
                                                }}">
                                        {{ $note->title ?? 'Untitled' }}
                                    </a>

                                    {{--                                    <a href="{{ route('course.notes.edit', [$currentCourse, $note]) }}"--}}
{{--                                       class="block rounded px-3 py-2 text-sm hover:bg-gray-100 truncate">--}}
{{--                                        {{ $note->title ?? 'Untitled' }}--}}
{{--                                    </a>--}}
                                @empty
                                    <div class="text-sm text-gray-500">No course notes yet.</div>
                                @endforelse
                            </nav>
                        </div>
                    @endif

                    <!-- Global Notes card -->
                    <div class="rounded-lg bg-white shadow p-4 overflow-hidden">
                        <div class="flex items-center justify-between mb-3 min-w-0">
                            <div class="text-sm font-semibold text-gray-700 truncate">
                                My Notes
                            </div>

                            <a href="{{ route('notes.create') }}"
                               class="text-xs text-blue-600 hover:underline shrink-0">
                                New
                            </a>
                        </div>

                        <nav class="space-y-2 min-w-0">
                            @forelse($sidebarGlobalNotes as $note)
                                @php
                                    $isActiveGlobalNote = request()->routeIs('notes.edit')
                                        && request()->route('note')?->id === $note->id;
                                @endphp

                                <a href="{{ route('notes.edit', $note) }}"
                                   class="block rounded px-3 py-2 text-sm truncate transition
                                                {{ $isActiveGlobalNote
                                                    ? 'bg-gray-200 text-gray-900 font-medium'
                                                    : 'hover:bg-gray-100 text-gray-700'
                                                }}">
                                    {{ $note->title ?? 'Untitled' }}
                                </a>

                                {{--                                <a href="{{ route('notes.edit', $note) }}"--}}
{{--                                   class="block rounded px-3 py-2 text-sm hover:bg-gray-100 truncate">--}}
{{--                                    {{ $note->title ?? 'Untitled' }}--}}
{{--                                </a>--}}
                            @empty
                                <div class="text-sm text-gray-500">No global notes yet.</div>
                            @endforelse
                        </nav>
                    </div>

                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
