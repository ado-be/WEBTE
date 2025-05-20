<x-app-layout>
    <x-slot name="header">
        <h2>Rozdeliť PDF na dve časti</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ url('/split_pdf') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="pdf_file" class="block font-medium">Vyber PDF súbor:</label>
                    <input type="file" name="pdf_file" id="pdf_file" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label for="split_at" class="block font-medium">Rozdeliť po strane číslo:</label>
                    <input type="number" name="split_at" id="split_at" class="form-control" required min="1">
                </div>

                <button type="submit" class="btn btn-primary">Rozdeliť PDF</button>
            </form>
        </div>
    </div>
</x-app-layout>
