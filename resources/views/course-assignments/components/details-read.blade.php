{{-- Assignment details card --}}
<div class="bg-white shadow-sm sm:rounded-lg">

    {{-- Top actions bar --}}
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Assignment details
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('course.assignments.show', [$course, $assignment]) }}?edit=1"
                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Edit
            </a>

            <form
                action="{{ route('course.assignments.destroy', [$course, $assignment]) }}"
                method="POST"
                onsubmit="return confirm('Delete this assignment? This cannot be undone.')"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50"
                >
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="p-4 space-y-6">

        {{-- Meta grid --}}
        <div class="rounded-lg border border-gray-200 p-2 bg-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">

                {{-- Created by --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Created by</div>
                    <div class="text-sm font-medium text-gray-900 truncate">
                        {{ $assignment->creator->name ?? 'Unknown' }}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        {{ $assignment->created_at->format('Y-m-d H:i') }}
                    </div>

                    @if($assignment->updated_at && $assignment->updated_at->ne($assignment->created_at))
                        <div class="text-xs text-gray-500 mt-1">
                            Updated: <span
                                class="text-gray-700">{{ $assignment->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Due --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Due</div>
                    @if($assignment->due_at)
                        <div class="text-sm font-medium text-gray-900">
                            {{ $assignment->due_at->format('Y-m-d H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500">
                            No due date
                        </div>
                    @endif
                </div>

                {{-- Max points --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Max points</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $assignment->max_points }}
                    </div>
                </div>

                {{-- Attempts --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Attempts</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $assignment->number_attempts ?? 'Unlimited' }}
                    </div>
                </div>

                {{-- Grading strategy --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Grading strategy</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $assignment->grading_strategy }}
                    </div>
                </div>

                {{-- Allow late --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Allow late</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $assignment->allow_late ? 'Yes' : 'No' }}
                    </div>
                </div>

                {{-- Published --}}
                <div class="rounded-md bg-white border border-gray-200 p-3">
                    <div class="text-xs text-gray-500">Published</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $assignment->is_published ? 'Yes' : 'No' }}
                    </div>
                </div>

                {{-- Spacer / future slot (keeps grid balanced nicely) --}}
                <div class="hidden lg:block"></div>
            </div>
        </div>

        <p class="text-gray-500 text-sm" style="margin-top: 0.25rem !important;">
            {{ $course->short_name }}
        </p>

        {{-- Title --}}
        <div style="margin-top: 0 !important;">
            <div class="prose max-w-none text-gray-800 text-xl">
                {!! nl2br(e($assignment->title)) !!}
            </div>
        </div>

        {{-- Description --}}
        <div>
            <div class="prose max-w-none text-gray-800 text-sm">
{{--                {!! nl2br(e($assignment->description)) !!}--}}
                <x-markdown :text="$assignment->description" class="prose-sm" />
            </div>
        </div>

        {{-- Submit button --}}
        <div class="flex justify-end">
            <a
                href="{{route('course.assignments.submissions.create',[$course,$assignment])}}"
                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
            >
                Add submission
            </a>
        </div>

    </div>
</div>
