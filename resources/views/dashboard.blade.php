<x-app-layout>



    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Uvítacia správa --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 mb-10">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            {{-- Admin sekcia - zobraz len ak je používateľ admin --}}
            @if(Auth::check() && Auth::user()->is_admin)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 mb-10">

                        <h3 class="text-lg font-bold mb-4">🛠 {{ __('Admin Tools') }}</h3>
                        <ul class="list-disc ml-6 space-y-2">
                            <li>
                                <a href="{{ route('admin.user-activities.index') }}" class="text-blue-500 hover:underline">
                                    🧾 {{ __('View user activity history') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user-activities.export') }}" class="text-blue-500 hover:underline">
                                    📤 {{ __('Export activities to CSV') }}
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete all activity history?') }}');" class="inline">
{{--                                <form action="{{ route('admin.user-activities.clear') }}" method="POST" onsubmit="return confirm('Naozaj chceš vymazať všetku históriu?');" class="inline">--}}
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:underline bg-transparent border-none p-0">
                                        🧹 {{ __('Clear all activities') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">📢 {{ __('Information') }}</h3>
                        <p>{{ __('You need admin privileges to view admin tools.') }}</p>
                    </div>
                </div>
            @endif
            <div class="h-10"></div>
        </div>
        <div class="w-full mt-12 flex flex-col items-center">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ url('/images-to-pdf') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Images to PDF') }}
                    </button>
                </a>
                <a href="{{ url('/merge_pdfs') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Merge PDF') }}
                    </button>
                </a>
                <a href="{{ url('/remove_page') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Remove Page') }}
                    </button>
                </a>
                <a href="{{ url('/protect_pdf') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Protect PDF') }}
                    </button>
                </a>
                <a href="{{ url('/pdf_to_word') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Convert to WORD') }}
                    </button>
                </a>
            </div>

            <div class="flex flex-wrap justify-center gap-4 mt-4">
                <a href="{{ url('/pdf_to_pptx') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Convert to PowerPoint') }}
                    </button>
                </a>
                <a href="{{ url('/split_pdf') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Split PDF') }}
                    </button>
                </a>
                <a href="{{ url('/extract_page') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Extract Page') }}
                    </button>
                </a>
                <a href="{{ url('/extract_text') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Extract Text') }}
                    </button>
                </a>
                <a href="{{ url('/pdf_to_images') }}">
                    <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                        {{ __('Convert PDF to Images') }}
                    </button>
                </a>
            </div>
        </div>



        {{--        <div class="mt-6">--}}
{{--            <a href="{{ url('/images-to-pdf') }}">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Images to PDF--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/merge_pdfs') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Zlúčiť PDF--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/remove_page') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Odstrániť stranu--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/protect_pdf') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Zaheslovať PDF--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/pdf_to_word') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Konvertovať do WORD--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/pdf_to_pptx') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Konvertovať do PowerPoint--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/split_pdf') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Rozdeľ PDF--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/extract_page') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Vytiahni stranu--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/extract_text') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Extrahuj text--}}
{{--                </button>--}}
{{--            </a>--}}

{{--            <a href="{{ url('/pdf_to_images') }}" class="ml-4">--}}
{{--                <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">--}}
{{--                    Extrahuj PDF do fotiek--}}
{{--                </button>--}}
{{--            </a>--}}
{{--        </div>--}}

    </div>


</x-app-layout>
