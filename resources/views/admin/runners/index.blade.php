@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6">
    <div class="flex items-baseline justify-between mb-6 pb-4 border-b-2 border-ink/10">
        <div>
            <h2 class="font-display text-2xl text-ink">Runners</h2>
            <p class="text-envelope text-sm mt-0.5">Delivery personnel and coverage zones</p>
        </div>
        <a href="{{ route('admin.runners.create') }}" class="bg-ink text-white px-4 py-2 rounded text-sm hover:bg-ink/90 transition">
            + Add runner
        </a>
    </div>

    <div class="space-y-2">
        @foreach ($runners as $runner)
            <div class="bg-white border border-ink/10 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium text-ink">{{ $runner->name }}</p>
                    <p class="text-sm text-envelope font-mono">{{ $runner->phone }}</p>
                    <p class="text-sm text-envelope">Zones: {{ $runner->zones->pluck('name')->join(', ') ?: 'None assigned' }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $runner->is_available ? 'bg-seal/10 text-seal' : 'bg-envelope/10 text-envelope' }}">
                    {{ $runner->is_available ? 'Available' : 'On delivery' }}
                </span>
            </div>
        @endforeach

        @if ($runners->isEmpty())
            <p class="text-envelope text-center py-12">No runners added yet.</p>
        @endif
    </div>
</div>
@endsection