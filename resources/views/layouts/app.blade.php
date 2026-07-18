<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PaperTrail</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-paper text-ink">
    <nav class="bg-ink text-paper px-6 py-4 flex justify-between items-center border-b-4 border-brass">
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full border-2 border-brass flex items-center justify-center text-brass font-display font-bold text-sm">PT</span>
            <span class="font-display text-xl tracking-tight">PaperTrail</span>
        </a>
        <div class="flex items-center gap-5 text-sm">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.requests.index') }}" class="text-paper/80 hover:text-brass transition">Requests</a>
                    <a href="{{ route('admin.requests.history') }}" class="text-paper/80 hover:text-brass transition">History</a>
                    <a href="{{ route('admin.runners.index') }}" class="text-paper/80 hover:text-brass transition">Runners</a>
                @endif
                @if(auth()->user()->role === 'runner')
                    <a href="{{ route('runner.dashboard') }}" class="text-paper/80 hover:text-brass transition relative">
                        Dashboard
                        <span id="runner-task-badge" style="display:none;" class="bg-stamp text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1"></span>
                    </a>
                @endif
                <span class="text-paper/50 font-mono text-xs">{{ auth()->user()->name }} · {{ auth()->user()->role }}</span>
                <a href="{{ route('profile.edit') }}" class="text-paper/80 hover:text-brass transition">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-stamp hover:text-stamp/80 transition">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-4 px-4 py-3 bg-seal/10 border border-seal/30 text-seal rounded flex items-center gap-2 text-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-seal"></span>
            {{ session('success') }}
        </div>
    @endif

    <main class="py-8">
        @yield('content')
    </main>
</body>
</html>