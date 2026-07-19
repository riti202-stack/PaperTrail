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
<body class="font-sans antialiased text-ink" style="background-color: #FDFBF7; background-image: radial-gradient(#14213D08 1px, transparent 1px); background-size: 22px 22px;">
    <div class="min-h-screen flex flex-col items-center justify-center px-6">
        <div class="mb-6">
            <a href="/">
                <x-application-logo />
            </a>
        </div>

        <div class="w-full max-w-md bg-white rounded-xl border border-ink/10 shadow-sm px-8 py-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>