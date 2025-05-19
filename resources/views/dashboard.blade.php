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
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    </div>
                </div>
            </div>

            {{-- Admin sekcia - zobraz len ak je používateľ admin --}}
            @if(Auth::check() && Auth::user()->is_admin)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">🛠 Admin nástroje</h3>
                        <ul class="list-disc ml-6 space-y-2">
                            <li>
                                <a href="{{ route('admin.user-activities.index') }}" class="text-blue-500 hover:underline">
                                    🧾 Zobraziť históriu aktivít používateľov
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user-activities.export') }}" class="text-blue-500 hover:underline">
                                    📤 Exportovať aktivity do CSV
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('Naozaj chceš vymazať všetku históriu?');" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:underline bg-transparent border-none p-0">
                                        🧹 Vymazať všetky aktivity
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">📢 Informácia</h3>
                        <p>Na zobrazenie admin nástrojov potrebujete admin práva.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

  
</x-app-layout>