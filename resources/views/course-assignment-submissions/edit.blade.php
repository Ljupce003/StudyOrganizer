<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit submission
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Assignment context --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100">
                    <div class="text-sm text-gray-500">Editing submission for</div>
                    <div class="text-lg font-semibold text-gray-900 mt-1">
                        {{ $assignment->title }}
                    </div>
                </div>
            </div>

            {{-- Edit form --}}
            <form
                action="{{ route('course.assignments.submissions.update', [$course, $assignment, $submission]) }}"
                method="POST"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf
                @method('PUT')

                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500">Your submission</div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('course.assignments.show', [$course, $assignment]) }}"
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
                        >
                            Save
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Content
                    </label>

                    <textarea
                        id="editor-content"
                        name="content"
                        rows="10"
                        class="w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                    >{{ old('content', $submission->content) }}</textarea>

                    @error('content')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </form>

        </div>
    </div>

</x-layouts.app-with-sidebar>
