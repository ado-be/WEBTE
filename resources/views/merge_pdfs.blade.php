<x-app-layout>
    <x-slot name="header">
        <h2>Zlúčiť dve PDF do jedného</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ url('/merge_pdfs') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="pdf_file1" class="block font-medium">Prvý PDF súbor:</label>
                    <input type="file" name="pdf_file1" id="pdf_file1" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label for="pdf_file2" class="block font-medium">Druhý PDF súbor:</label>
                    <input type="file" name="pdf_file2" id="pdf_file2" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Zlúčiť PDF</button>
            </form>
        </div>
    </div>
</x-app-layout>
