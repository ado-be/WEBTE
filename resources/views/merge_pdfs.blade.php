<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ __('Zlúčiť dve PDF do jedného') }}</h2>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/merge_pdfs') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="pdf_file1" class="block font-medium">{{ __('Prvý PDF súbor:') }}</label>
                <input type="file" name="pdf_file1" id="pdf_file1" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="pdf_file2" class="block font-medium">{{ __('Druhý PDF súbor:') }}</label>
                <input type="file" name="pdf_file2" id="pdf_file2" class="w-full border rounded px-3 py-2" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                {{ __('Zlúčiť PDF') }}
            </button>
        </form>
    </div>
</x-app-layout>
