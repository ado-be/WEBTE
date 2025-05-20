<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Uvítacia správa --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            {{-- Admin sekcia - zobraz len ak je používateľ admin --}}
            @if(Auth::check() && Auth::user()->is_admin)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">🛠 {{ __('Admin Tools') }}</h3>
                        <ul class="list-disc ml-6 space-y-2">
                            <li>
                                <a href="{{ route('admin.user-activities.index') }}" class="text-blue-500 hover:underline">
                                    🧾 {{ __('View user activity history') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user-activities.export') }}" class="text-blue-500 hover:underline">
                                    📤 {{ __('Export activities to CSV') }}
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete all activity history?') }}');" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:underline bg-transparent border-none p-0">
                                        🧹 {{ __('Clear all activities') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">📢 {{ __('Information') }}</h3>
                        <p>{{ __('You need admin privileges to view admin tools.') }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
