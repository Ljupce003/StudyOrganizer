<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">
            <main class="flex-1">
                {{ $slot }}
            </main>

            <aside class="w-64 shrink-0 space-y-6">
                <!-- Courses card -->
                <div class="rounded-lg bg-white shadow p-4">
                    <div class="text-sm font-semibold text-gray-700 mb-3">My Courses</div>
                    <nav class="space-y-2">
                        @forelse($sidebarCourses as $course)
                            <a href="{{ route('course.show', $course) }}"
                               class="block rounded px-3 py-2 text-sm hover:bg-gray-100">
                                {{ $course->short_name }}
                            </a>
                        @empty
                            <div class="text-sm text-gray-500">No enrolled courses.</div>
                        @endforelse
                    </nav>
                </div>

                <!-- Course Notes card -->
                @if($currentCourse)
                    <div class="rounded-lg bg-white shadow p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-semibold text-gray-700">
                                Course Notes
                            </div>

                            <a href="{{ route('course.notes.create', $currentCourse) }}"
                               class="text-xs text-blue-600 hover:underline">
                                New
                            </a>
                        </div>

                        <nav class="space-y-2">
                            @forelse($sidebarCourseNotes as $note)
                                <a href="{{ route('course.notes.edit', [$currentCourse, $note]) }}"
                                   class="block rounded px-3 py-2 text-sm hover:bg-gray-100">
                                    {{ \Illuminate\Support\Str::limit($note->title ?? 'Untitled', 22) }}
                                </a>
                            @empty
                                <div class="text-sm text-gray-500">No course notes yet.</div>
                            @endforelse
                        </nav>
                    </div>
                @endif


                <!-- Global Notes card -->
                <div class="rounded-lg bg-white shadow p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm font-semibold text-gray-700">
                            My Notes
                        </div>

                        <a href="{{ route('notes.create') }}"
                           class="text-xs text-blue-600 hover:underline">
                            New
                        </a>
                    </div>

                    <nav class="space-y-2">
                        @forelse($sidebarGlobalNotes as $note)
                            <a href="{{ route('notes.edit', $note) }}"
                               class="block rounded px-3 py-2 text-sm hover:bg-gray-100">
                                {{ \Illuminate\Support\Str::limit($note->title ?? 'Untitled', 22) }}
                            </a>
                        @empty
                            <div class="text-sm text-gray-500">No global notes yet.</div>
                        @endforelse
                    </nav>
                </div>

            </aside>
        </div>
    </div>
</x-app-layout>
