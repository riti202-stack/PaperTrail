@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <h2 class="font-display text-2xl text-ink">Add runner</h2>
        <p class="text-envelope text-sm mt-0.5">Create a delivery personnel account</p>
    </div>

    @if ($errors->any())
        <div class="bg-stamp/10 border border-stamp/30 text-stamp p-3 rounded mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.runners.store') }}" class="bg-white border border-ink/10 rounded-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Password</label>
            <input type="password" name="password" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Vehicle (optional)</label>
            <input type="text" name="vehicle_no" value="{{ old('vehicle_no') }}" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0">
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Coverage zones</label>
            <div class="space-y-1.5">
                @foreach ($zones as $zone)
                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="zone_ids[]" value="{{ $zone->id }}" {{ in_array($zone->id, old('zone_ids', [])) ? 'checked' : '' }} class="rounded border-ink/30 text-ink focus:ring-0">
                        {{ $zone->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full bg-ink text-white px-4 py-2.5 rounded font-medium hover:bg-ink/90 transition">
            Create runner account
        </button>
    </form>
</div>
@endsection