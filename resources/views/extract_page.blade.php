<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Extrahovať konkrétnu stranu z PDF') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/extract_page') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="pdf_file" class="block font-medium">{{ __('Vyber PDF súbor:') }}</label>
                    <input type="file" name="pdf_file" id="pdf_file" class="w-full border rounded px-3 py-2 mt-1" required>
                </div>

                <div class="mb-4">
                    <label for="page_number" class="block font-medium">{{ __('Číslo strany na extrakciu:') }}</label>
                    <input type="number" name="page_number" id="page_number" class="w-full border rounded px-3 py-2 mt-1" required min="1">
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    {{ __('Extrahovať stranu') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
