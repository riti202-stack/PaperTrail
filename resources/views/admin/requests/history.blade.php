@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6">
    <div class="flex items-baseline justify-between mb-6 pb-4 border-b-2 border-ink/10">
        <div>
            <h2 class="font-display text-2xl text-ink">History</h2>
            <p class="text-envelope text-sm mt-0.5">Rejected and completed requests</p>
        </div>
        <a href="{{ route('admin.requests.index') }}" class="text-sm text-envelope hover:text-ink transition">
            ← Back to active
        </a>
    </div>

    <div class="space-y-2">
        @foreach ($requests as $request)
            <div class="bg-white/60 border border-ink/10 rounded-lg p-4 flex justify-between items-center opacity-80">
                <div class="flex gap-4 items-center">
                    <span class="font-mono text-xs text-envelope">#{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <p class="text-sm text-ink">{{ $request->document_type }} — {{ $request->requester->name }}</p>
                        <p class="text-xs text-envelope">{{ $request->delivery_address }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $request->status === 'rejected' ? 'bg-stamp/10 text-stamp' : 'bg-seal/10 text-seal' }}">
                        {{ ucfirst($request->status) }}
                    </span>
                    <span class="text-xs text-envelope font-mono">{{ $request->archived_at->format('d M Y') }}</span>
                </div>
            </div>
        @endforeach

        @if ($requests->isEmpty())
            <p class="text-envelope text-center py-12">No archived requests yet.</p>
        @endif
    </div>
</div>
@endsection