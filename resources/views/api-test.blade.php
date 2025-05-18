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
                    <h3 class="text-lg font-medium mb-4">{{ __('Testovanie API') }}</h3>

                    <div class="mb-6">
                        <p>1. Vytvorte token v sekcii <a href="{{ route('tokens.index') }}" class="text-blue-500 hover:underline">API Tokeny</a></p>
                        <p>2. Skopírujte vytvorený token a vložte ho sem (vrátane "Bearer " pred tokenom)</p>
                        <p>3. Kliknite na tlačidlá nižšie na testovanie API</p>
                    </div>

                    <div class="mb-4">
                        <label for="api-token" class="block text-sm font-medium">Váš API Token</label>
                        <input type="text" id="api-token" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="Bearer váš_token" />
                    </div>

                    <div class="space-y-4 mb-6">
                        <button id="test-user" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Získať údaje o používateľovi
                        </button>

                        <button id="test-activities" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Získať aktivity
                        </button>

                        @if(Auth::user()->is_admin)
                            <button id="test-export" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                Exportovať aktivity
                            </button>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h4 class="font-medium mb-2">Výsledok:</h4>
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

            // Funkcia na vykonanie API požiadavky
            async function makeRequest(url, method = 'GET') {
                const token = tokenInput.value.trim();
                if (!token) {
                    resultDisplay.textContent = 'Zadajte platný token';
                    return;
                }

                try {
                    resultDisplay.textContent = 'Načítavam...';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': token.startsWith('Bearer ') ? token : `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        resultDisplay.textContent = `Chyba ${response.status}: ${errorText}`;
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
                    resultDisplay.textContent = `Chyba: ${error.message}`;
                }
            }

            // Tlačidlo na získanie údajov o používateľovi
            document.getElementById('test-user').addEventListener('click', function() {
                makeRequest('/api/user');
            });

            // Tlačidlo na získanie aktivít
            document.getElementById('test-activities').addEventListener('click', function() {
                makeRequest('/api/user-activities');
            });

            // Tlačidlo na export aktivít (len pre adminov)
            const exportButton = document.getElementById('test-export');
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    const token = tokenInput.value.trim();
                    if (!token) {
                        resultDisplay.textContent = 'Zadajte platný token';
                        return;
                    }

                    // Extrahujeme token bez "Bearer "
                    const authToken = token.startsWith('Bearer ') ? token.substring(7) : token;

                    // Pre export otvoríme nové okno alebo tab
                    window.open(`/api/user-activities/export?token=${authToken}`, '_blank');
                });
            }
        });
    </script>
</x-app-layout>