@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <div class="flex items-baseline justify-between mb-6 pb-4 border-b-2 border-ink/10">
        <div>
            <h2 class="font-display text-2xl text-ink">My requests</h2>
            <p class="text-envelope text-sm mt-0.5">Document courier requests you've submitted</p>
        </div>
        <a href="{{ route('requester.create') }}" class="bg-ink text-white px-4 py-2 rounded text-sm hover:bg-ink/90 transition">
            + New request
        </a>
    </div>

    @if ($requests->isEmpty())
        <div class="text-center py-16">
            <div class="w-12 h-12 rounded-full border-2 border-brass mx-auto mb-3 flex items-center justify-center text-brass">✎</div>
            <p class="text-envelope">You haven't made any requests yet.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($requests as $request)
                <a href="{{ route('requester.track', $request) }}" class="block bg-white border border-ink/10 rounded-lg p-4 hover:border-ink/30 transition">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4 items-center">
                            <span class="font-mono text-xs text-envelope">#PT-{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <p class="font-medium text-ink">{{ $request->document_type }}</p>
                                <p class="text-sm text-envelope">{{ $request->delivery_address }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                @if($request->status === 'rejected') bg-stamp/10 text-stamp
                                @elseif($request->status === 'delivered') bg-seal/10 text-seal
                                @elseif(in_array($request->status, ['assigned','accepted','picked_up','in_transit'])) bg-brass/10 text-brass
                                @else bg-envelope/10 text-envelope @endif">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                            <span class="text-envelope">→</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection