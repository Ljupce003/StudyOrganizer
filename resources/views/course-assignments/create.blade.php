@php use App\Enums\GradingStrategy; @endphp
<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create assignment
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form
                action="{{ route('course.assignments.store', $course) }}"
                method="POST"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf

                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500">Assignment details</div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('courses.show', $course) }}"
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
                        >
                            Create
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-6">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            required
                        >
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            name="description"
                            rows="7"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            required
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fields grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max points</label>
                            <input
                                type="number"
                                name="max_points"
                                min="1"
                                max="500"
                                value="{{ old('max_points', 100) }}"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                                required
                            >
                            @error('max_points') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attempts (optional)</label>
                            <input
                                type="number"
                                name="number_attempts"
                                min="1"
                                value="{{ old('number_attempts') }}"
                                placeholder="Unlimited"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            >
                            @error('number_attempts') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due (optional)</label>
                            <input
                                type="datetime-local"
                                name="due_at"
                                value="{{ old('due_at') }}"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            >
                            @error('due_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grading strategy</label>
                            <select
                                name="grading_strategy"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                                required
                            >
                                @foreach(GradingStrategy::cases() as $strategy)
                                    <option
                                        value="{{ $strategy->value }}"
                                        {{ old('grading_strategy', GradingStrategy::FIRST->value) === $strategy->value ? 'selected' : '' }}
                                    >
                                        {{ $strategy->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grading_strategy') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <input
                                id="allow_late"
                                type="checkbox"
                                name="allow_late"
                                value="1"
                                class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                                {{ old('allow_late') ? 'checked' : '' }}
                            >
                            <label for="allow_late" class="text-sm text-gray-700">Allow late</label>
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <input
                                id="is_published"
                                type="checkbox"
                                name="is_published"
                                value="1"
                                class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                                {{ old('is_published') ? 'checked' : '' }}
                            >
                            <label for="is_published" class="text-sm text-gray-700">Published</label>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>
</x-layouts.app-with-sidebar>
