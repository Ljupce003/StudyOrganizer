<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New submission
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Assignment context --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100">
                    <div class="text-sm text-gray-500">Submitting for</div>
                    <div class="text-lg font-semibold text-gray-900 mt-1">
                        {{ $assignment->title }}
                    </div>
                </div>

                <div class="p-4">
                    <div class="rounded-lg border border-gray-200 p-2 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">

                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Created by</div>
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $assignment->creator->name ?? 'Unknown' }}
                                </div>
                            </div>

                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Due</div>
                                @if($assignment->due_at)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $assignment->due_at->format('Y-m-d H:i') }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">No due date</div>
                                @endif
                            </div>

                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Max points</div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $assignment->max_points }}
                                </div>
                            </div>

                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Attempts</div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $assignment->number_attempts ?? 'Unlimited' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    @if($assignment->description)
                        <div class="mt-4 text-sm text-gray-700">
                            <div class="text-xs text-gray-500 mb-1">Assignment description</div>
                            <div class="rounded-md border border-gray-200 p-3 bg-white">
                                {!! nl2br(e($assignment->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Submission form --}}
            <form
                action="{{ route('course.assignments.submissions.store', [$course, $assignment]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf

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
                            Submit
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
                    >{{ old('content') }}</textarea>

                    @error('content')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="pt-2 space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Attach files (optional)
                        </label>

                        <input
                            type="file"
                            name="files[]"
                            multiple
                            class="block w-full text-sm text-gray-700
               file:mr-4 file:py-2 file:px-4
               file:rounded-md file:border-0
               file:text-sm file:font-medium
               file:bg-gray-900 file:text-white
               hover:file:bg-gray-700"
                        >

                        @error('files')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('files.*')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-gray-500">
                            You can select multiple files. Each file must be under 10MB.
                        </p>
                    </div>


                </div>
            </form>

        </div>
    </div>

</x-layouts.app-with-sidebar>
