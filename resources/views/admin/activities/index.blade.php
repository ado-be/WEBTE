<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Activity History') }}
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
                        <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete all activity history?') }}');">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                🧹 {{ __('Clear history') }}
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700">
                            <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">{{ __('User') }}</th>
                                <th class="py-2 px-4 border-b">{{ __('Action') }}</th>
                                <th class="py-2 px-4 border-b">{{ __('Source') }}</th>
                                <th class="py-2 px-4 border-b">{{ __('IP') }}</th>
                                <th class="py-2 px-4 border-b">{{ __('Location') }}</th>
                                <th class="py-2 px-4 border-b">{{ __('Time') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $activity->user->email ?? __('Unknown') }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->action }}</td>
                                    <td class="py-2 px-4 border-b">{{ strtoupper($activity->source) }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->ip }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->location }}</td>
                                    <td class="py-2 px-4 border-b">{{ $activity->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-2 px-4 border-b text-center">{{ __('No activities found') }}</td>
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
