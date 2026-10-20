<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Biblioteca de Medios | Panel de Gestión</title>
    
    @include('partials.favicon')

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 overflow-hidden select-none">

    <div 
        x-data="{}" 
        x-init="$nextTick(() => { window.dispatchEvent(new CustomEvent('open-media-library')); })"
        class="h-screen w-screen flex flex-col justify-between"
    >
        <!-- Header -->
        <header class="h-14 bg-slate-950 border-b border-slate-800 px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/admin" class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all flex items-center gap-1.5 text-xs font-semibold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Regresar al Panel</span>
                </a>
                <div class="h-4 w-px bg-slate-800"></div>
                <span class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                    Biblioteca Multimedia de la Clínica
                </span>
            </div>

            <button 
                type="button" 
                @click="window.dispatchEvent(new CustomEvent('open-media-library'))" 
                class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-sky-500/20 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="18" height="18" x="3" y="3" rx="2" stroke-width="2"/><circle cx="9" cy="9" r="2" stroke-width="2"/><path stroke-width="2" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <span>Abrir Biblioteca</span>
            </button>
        </header>

        <!-- Media Library Modal (Always Available) -->
        @include('admin.media-library.modal')
    </div>

</body>
</html>
