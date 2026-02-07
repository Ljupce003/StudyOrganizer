{{--<x-app-layout>--}}
    <x-layouts.app-with-sidebar>
        <div class="rounded-lg bg-white shadow p-6">
            <h1 class="text-xl font-semibold mb-1">New Course Note</h1>
            <div class="text-sm text-gray-500 mb-4">{{ $course->short_name }}</div>

            <form method="POST" action="{{ route('courses.notes.store', $course) }}" class="space-y-4">
                @csrf

                <input name="title" value="{{ old('title') }}" placeholder="Title (optional)"
                       class="w-full rounded border-gray-300"/>

                <textarea id="note-content" name="content" class="w-full">{{ old('content') }}</textarea>

                @error('content')
                <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                <div class="flex justify-end">
                    <button class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </x-layouts.app-with-sidebar>
{{--</x-app-layout>--}}
