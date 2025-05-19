<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @include('layouts.navigation')


    <!-- Language Switcher -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex justify-end">
        <div class="language-switcher">
            <a href="{{ route('lang.switch', ['lang' => 'sk']) }}" class="{{ app()->getLocale() == 'sk' ? 'font-bold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">SK</a>
            <span class="mx-1 text-gray-400">|</span>
            <a href="{{ route('lang.switch', ['lang' => 'en']) }}" class="{{ app()->getLocale() == 'en' ? 'font-bold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">EN</a>
        </div>
    </div>
    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

</div>
</body>
</html>