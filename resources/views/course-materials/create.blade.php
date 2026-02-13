<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload material
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form
                action="{{ route('course.materials.store', $course) }}"
                method="POST"
                enctype="multipart/form-data"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf

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
                            Upload
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-6">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Title <span class="text-gray-400">(optional)</span>
                        </label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="e.g. Week 3 — Database Indexes"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            If left empty, we’ll use the file name (without extension).
                        </p>
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- File --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            File <span class="text-red-600">*</span>
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
                            required
                        >

                        <p class="text-xs text-gray-500 mt-1">
                            Upload PDFs, slides, images, or archives (depending on validation rules). Max size depends on server config.
                        </p>

                        @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Options --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-2">
                            <input
                                id="is_published"
                                type="checkbox"
                                name="is_published"
                                value="1"
                                class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                                {{ old('is_published', '1') ? 'checked' : '' }}
                            >
                            <label for="is_published" class="text-sm text-gray-700">
                                Published
                            </label>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-layouts.app-with-sidebar>
