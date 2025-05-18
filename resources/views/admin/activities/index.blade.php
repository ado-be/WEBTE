<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('História aktivít používateľov') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('Naozaj chceš vymazať celú históriu?');">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                🧹 Vymazať históriu
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700">
                            <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Používateľ</th>
                                <th class="py-2 px-4 border-b">Akcia</th>
                                <th class="py-2 px-4 border-b">Zdroj</th>
                                <th class="py-2 px-4 border-b">IP</th>
                                <th class="py-2 px-4 border-b">Lokácia</th>
                                <th class="py-2 px-4 border-b">Čas</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $activity->user->email ?? 'Neznámy' }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->action }}</td>
                                    <td class="py-2 px-4 border-b">{{ strtoupper($activity->source) }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->ip }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->location }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-2 px-4 border-b text-center">Žiadne aktivity nenájdené</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>