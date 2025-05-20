<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Odstránenie strany z PDF
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Laravel validácia --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Výpis chýb zo session --}}
{{--            @if(session('error') || session('error_stderr') || session('error_stdout') || session('error_trace'))--}}
{{--                <div class="alert alert-danger mb-4">--}}
{{--                    <h4>⚠️ Chyba pri spracovaní:</h4>--}}

{{--                    @if(session('error'))--}}
{{--                        <p><strong>Správa:</strong> {{ session('error') }}</p>--}}
{{--                    @endif--}}

{{--                    @if(session('error_exit'))--}}
{{--                        <p><strong>Exit kód:</strong> {{ session('error_exit') }}</p>--}}
{{--                    @endif--}}

{{--                    @if(session('error_stderr'))--}}
{{--                        <h5>Stderr:</h5>--}}
{{--                        <pre style="white-space: pre-wrap;">{{ session('error_stderr') }}</pre>--}}
{{--                    @endif--}}

{{--                    @if(session('error_stdout'))--}}
{{--                        <h5>Stdout:</h5>--}}
{{--                        <pre style="white-space: pre-wrap;">{{ session('error_stdout') }}</pre>--}}
{{--                    @endif--}}

{{--                    @if(session('error_trace'))--}}
{{--                        <h5>Laravel výnimka:</h5>--}}
{{--                        <pre style="white-space: pre-wrap;">{{ session('error_trace') }}</pre>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            @endif--}}

            {{-- Formulár --}}
            <form action="{{ url('/remove_page') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf

                <div class="mb-4">
                    <label for="pdf_file" class="block font-medium">Vyber PDF súbor:</label>
                    <input type="file" name="pdf_file" id="pdf_file" class="form-control mt-1 block w-full" required>
                </div>

                <div class="mb-4">
                    <label for="page" class="block font-medium">Číslo strany (0 = prvá):</label>
                    <input type="number" name="page" id="page" class="form-control mt-1 block w-full" required min="0">
                </div>

                <div class="flex justify-start">
                    <button type="submit" class="btn btn-danger">
                        Odstrániť stránku
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
