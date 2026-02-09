{{--<x-app-layout>--}}
<x-layouts.app-with-sidebar>
    <div class="rounded-lg bg-white shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">{{ $note->title ?? 'Untitled' }}</h1>

            <form method="POST" action="{{ route('notes.destroy', $note) }}"
                  onsubmit="return confirm('Delete this note?')">
                @csrf
                @method('DELETE')
                <button class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>

        @if(session('status'))
            <div class="mb-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('notes.update', $note) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <input
                name="title"
                value="{{ old('title', $note->title) }}"
                placeholder="Title (optional)"
                class="w-full rounded border-gray-300"
            />

            <textarea
                id="editor-content"
                name="content"
                class="w-full"
            >{{ old('content', $note->content) }}</textarea>

            @error('content')
            <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror

            <div class="flex justify-end">
                <button class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-layouts.app-with-sidebar>
{{--</x-app-layout>--}}
