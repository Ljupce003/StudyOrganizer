@php use App\Enums\GradingStrategy; @endphp
<form
    action="{{ route('course.assignments.update', [$course, $assignment]) }}"
    method="POST"
    class="bg-white shadow-sm sm:rounded-lg"
>
    @csrf
    @method('PUT')

    {{-- Top bar: Save / Cancel --}}
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-500">Edit assignment</div>

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

    <div class="p-4 space-y-6">

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input
                name="title"
                value="{{ old('title', $assignment->title) }}"
                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                required
            >
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
                id="editor-content"
                name="description"
                rows="6"
                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                required
            >{{ old('description', $assignment->description) }}</textarea>
            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Grid fields --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700">Max points</label>
                <input
                    type="number"
                    name="max_points"
                    min="1"
                    value="{{ old('max_points', $assignment->max_points) }}"
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
                    value="{{ old('number_attempts', $assignment->number_attempts) }}"
                    class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                    placeholder="Unlimited"
                >
                @error('number_attempts') <p
                    class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Due</label>
                <input
                    type="datetime-local"
                    name="due_at"
                    value="{{ old('due_at', $assignment->due_at?->format('Y-m-d\TH:i')) }}"
                    class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                >
                @error('due_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Grading strategy</label>
                <select
                    name="grading_strategy"
                    value="{{ old('grading_strategy', $assignment->grading_strategy) }}"
                    class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                >
                    @foreach(GradingStrategy::cases() as $strategy)
                        <option
                            value="{{ $strategy->value }}"
                            {{ $strategy->value === old('grading_strategy', $assignment->grading_strategy) ? "selected" : "" }}
                        >{{ $strategy->name }}</option>
                    @endforeach
                </select>
                @error('grading_strategy') <p
                    class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 mt-6">
                <input
                    type="checkbox"
                    name="allow_late"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                    {{ old('allow_late', $assignment->allow_late) ? 'checked' : '' }}
                >
                <span class="text-sm text-gray-700">Allow late</span>
            </label>

            <label class="flex items-center gap-2 mt-6">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                    {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}
                >
                <span class="text-sm text-gray-700">Published</span>
            </label>

        </div>
    </div>
</form>
