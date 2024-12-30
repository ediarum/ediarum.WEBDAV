<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ediarum WebDAV') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-ediarum-gray">
    @if((auth()->user()))
        @include('layouts.navigation')
    @endif

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $header }}
                </h2>
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                @if($errors->any())
                @foreach($errors->all() as $e)
                    <p class="bg-red-600 text-white text-xl">{{$e}}</p>
                @endforeach
                @endif
                {{ $slot }}
            </div>
        </div>
    </main>
</div>
</body>
</html>
