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

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2246%22 fill=%22%2314213D%22 stroke=%22%23C99A3B%22 stroke-width=%226%22/><text x=%2250%22 y=%2263%22 font-family=%22Georgia,serif%22 font-size=%2238%22 font-weight=%22bold%22 fill=%22%23C99A3B%22 text-anchor=%22middle%22>PT</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- <body class="font-sans antialiased bg-paper text-ink"> -->
<body class="font-sans antialiased text-ink min-h-screen flex flex-col" style="background-color: #FDFBF7; background-image: radial-gradient(#14213D08 1px, transparent 1px); background-size: 22px 22px;">

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

                @if(auth()->user()->role === 'requester')
    <a href="{{ route('requester.index') }}" class="text-paper/80 hover:text-brass transition">My requests</a>
    <a href="{{ route('requester.create') }}" class="text-paper/80 hover:text-brass transition">New request</a>
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

    <footer class="mt-20 border-t border-ink/10" style="background-color: #14213D05;">
        <div class="max-w-5xl mx-auto px-6 py-10 grid grid-cols-1 sm:grid-cols-3 gap-8 items-start">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-7 h-7 rounded-full border-2 border-brass flex items-center justify-center text-brass font-display text-xs">PT</span>
                    <span class="font-display text-lg text-ink">PaperTrail</span>
                </div>
                <p class="text-xs text-envelope leading-relaxed">
                    A document courier tracking system built for reliable, traceable delivery.
                </p>
            </div>

            <div>
                <p class="text-xs font-mono uppercase tracking-wide text-brass mb-2">Navigate</p>
                <div class="flex flex-col gap-1.5 text-sm text-envelope">
                    <a href="{{ url('/dashboard') }}" class="hover:text-ink transition">Dashboard</a>
                    @auth
                        @if(auth()->user()->role === 'requester')
                            <a href="{{ route('requester.create') }}" class="hover:text-ink transition">New request</a>
                        @endif
                    @endauth
                    <a href="{{ route('profile.edit') }}" class="hover:text-ink transition">Profile</a>
                </div>
            </div>

            <div class="sm:text-right">
                <p class="text-xs font-mono uppercase tracking-wide text-brass mb-2">Project</p>
                <p class="text-sm text-envelope">CSE 3100 · Web Programming Lab</p>
                <p class="text-xs text-envelope font-mono mt-1">&copy; {{ date('Y') }} PaperTrail</p>
            </div>
        </div>
    </footer>
</body>
</html>