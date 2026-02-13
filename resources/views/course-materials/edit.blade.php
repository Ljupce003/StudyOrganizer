<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit material
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form
                action="{{ route('course.materials.update', [$course, $material]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf
                @method('PUT')

                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500">Material details</div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('course.show', $course) }}"
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

                <div class="p-4 space-y-6">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Title
                        </label>
                        <input
                            name="title"
                            value="{{ old('title', $material->title) }}"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            required
                        >
                        @error('title')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- File info (read-only) --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 text-sm">
                        <a href="{{ route("course.materials.fetch",[$course,$material,$material->original_filename]) }}">
                            <div class="flex flex-col gap-1">
                                <div>
                                    <span class="font-medium text-gray-700">Filename:</span>
                                    {{ $material->original_filename }}
                                </div>

                                @if($material->mime_type)
                                    <div>
                                        <span class="font-medium text-gray-700">MIME type:</span>
                                        <span class="font-mono text-xs">{{ $material->mime_type }}</span>
                                    </div>
                                @endif

                                @if($material->size_bytes)
                                    <div>
                                        <span class="font-medium text-gray-700">Size:</span>
                                        {{ number_format($material->size_bytes / 1024, 1) }} KB
                                    </div>
                                @endif

                                <div>
                                    <span class="font-medium text-gray-700">Uploaded:</span>
                                    {{ $material->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                        </a>

                    </div>

                    {{-- Replace file --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Replace file
                        </label>

                        <input
                            type="file"
                            name="file"
                            class="mt-1 block w-full text-sm text-gray-700
               file:mr-4 file:py-2 file:px-4
               file:rounded-md file:border-0
               file:text-sm file:font-medium
               file:bg-gray-900 file:text-white
               hover:file:bg-gray-700"
                        >

                        <p class="text-xs text-gray-500 mt-1">
                            If you upload a new file, the old one will be permanently removed.
                        </p>

                        @error('file')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Published toggle --}}
                    <div class="flex items-center gap-2">
                        <input
                            id="is_published"
                            type="checkbox"
                            name="is_published"
                            value="1"
                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                            {{ old('is_published', $material->is_published) ? 'checked' : '' }}
                        >
                        <label for="is_published" class="text-sm text-gray-700">
                            Published
                        </label>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-layouts.app-with-sidebar>
