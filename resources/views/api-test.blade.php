<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('API Test') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">{{ __('API Testing') }}</h3>

                    <div class="mb-6">
                        <p>1. {{ __('Create a token in the') }} <a href="{{ route('tokens.index') }}" class="text-blue-500 hover:underline">{{ __('API Tokens') }}</a></p>
                        <p>2. {{ __('Copy the created token and paste it here (including "Bearer " before the token)') }}</p>
                        <p>3. {{ __('Click the buttons below to test the API') }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="api-token" class="block text-sm font-medium">{{ __('Your API Token') }}</label>
                        <input type="text" id="api-token" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="{{ __('Bearer your_token') }}" />
                    </div>

                    <div class="space-y-4 mb-6">
                        <button id="test-user" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            {{ __('Get user data') }}
                        </button>

                        <button id="test-activities" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            {{ __('Get activities') }}
                        </button>

                        @if(Auth::user()->is_admin)
                            <button id="test-export" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                {{ __('Export activities') }}
                            </button>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h4 class="font-medium mb-2">{{ __('Result:') }}</h4>
                        <pre id="result" class="bg-gray-100 dark:bg-gray-900 p-4 rounded overflow-x-auto max-h-96 text-sm"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tokenInput = document.getElementById('api-token');
            const resultDisplay = document.getElementById('result');

            async function makeRequest(url, method = 'GET') {
                const token = tokenInput.value.trim();
                if (!token) {
                    resultDisplay.textContent = '{{ __('Please enter a valid token') }}';
                    return;
                }

                try {
                    resultDisplay.textContent = '{{ __('Loading...') }}';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': token.startsWith('Bearer ') ? token : `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        resultDisplay.textContent = `{{ __('Error') }} ${response.status}: ${errorText}`;
                        return;
                    }

                    if (response.headers.get('content-type')?.includes('application/json')) {
                        const data = await response.json();
                        resultDisplay.textContent = JSON.stringify(data, null, 2);
                    } else {
                        const text = await response.text();
                        resultDisplay.textContent = text;
                    }
                } catch (error) {
                    resultDisplay.textContent = `{{ __('Error') }}: ${error.message}`;
                }
            }

            document.getElementById('test-user').addEventListener('click', function() {
                makeRequest('/api/user');
            });

            document.getElementById('test-activities').addEventListener('click', function() {
                makeRequest('/api/user-activities');
            });

            const exportButton = document.getElementById('test-export');
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    const token = tokenInput.value.trim();
                    if (!token) {
                        resultDisplay.textContent = '{{ __('Please enter a valid token') }}';
                        return;
                    }

                    const authToken = token.startsWith('Bearer ') ? token.substring(7) : token;
                    window.open(`/api/user-activities/export?token=${authToken}`, '_blank');
                });
            }
        });
    </script>
</x-app-layout>
