<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Entrar' }} — EventFlow</title>
    <meta name="description" content="EventFlow - Plataforma completa para gestão de eventos.">
    <meta name="theme-color" content="#09090b">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-bg min-h-screen bg-zinc-950 text-zinc-100 antialiased">
    {{ $slot }}
    <x-toast-stack />
</body>
</html>
