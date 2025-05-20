<x-app-layout>
    <x-slot name="header">
        <h2>Konverzia PDF do PowerPointu</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ url('/pdf_to_pptx') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="pdf_file" class="block font-medium">Vyber PDF súbor:</label>
                    <input type="file" name="pdf_file" id="pdf_file" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Konvertovať na PowerPoint</button>
            </form>
        </div>
    </div>
</x-app-layout>
