<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Odstránenie strany z PDF') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">

            {{-- Laravel validácia --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    <ul class="mb-0 list-disc list-inside">
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
                    <label for="pdf_file" class="block font-medium">{{ __('Vyber PDF súbor:') }}</label>
                    <input type="file" name="pdf_file" id="pdf_file" class="w-full border rounded px-3 py-2 mt-1" required>
                </div>

                <div class="mb-4">
                    <label for="page" class="block font-medium">{{ __('Číslo strany (0 = prvá):') }}</label>
                    <input type="number" name="page" id="page" class="w-full border rounded px-3 py-2 mt-1" required min="0">
                </div>

                <div class="flex justify-start">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        {{ __('Odstrániť stránku') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
