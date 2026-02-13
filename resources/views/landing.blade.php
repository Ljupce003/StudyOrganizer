<x-app-layout>
    <div class="min-h-screen bg-gray-50 flex flex-col">


        {{-- Hero --}}
        <main class="flex-1 flex items-center justify-center px-6">
            <div class="max-w-3xl text-center space-y-6">
                <h1 class="text-4xl font-bold text-gray-900">
                    Organize courses. Track assignments. Stay focused.
                </h1>

                <p class="text-lg text-gray-600">
                    StudyOrganizer helps students and professors manage coursework,
                    materials, and submissions in one structured environment.
                </p>

                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-md text-base font-medium hover:bg-gray-700"
                    >
                        Go to Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-md text-base font-medium hover:bg-gray-700"
                    >
                        Get Started
                    </a>
                @endauth
            </div>
        </main>

    </div>
</x-app-layout>
