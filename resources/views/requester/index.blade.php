


@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <div class="flex items-baseline justify-between mb-8 pb-4 border-b-2 border-ink/10">
        <div>
            <h2 class="font-display text-2xl text-ink">My requests</h2>
            <p class="text-envelope text-sm mt-0.5">Document courier requests you've submitted</p>
        </div>
        <a href="{{ route('requester.create') }}" class="bg-ink text-white px-4 py-2 rounded text-sm hover:bg-ink/90 transition shadow-sm">
            + New request
        </a>
    </div>

    @if ($requests->isEmpty())
        <div class="text-center py-20">
            <div class="w-12 h-12 rounded-full border-2 border-brass mx-auto mb-3 flex items-center justify-center text-brass">✎</div>
            <p class="text-envelope">You haven't made any requests yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($requests as $request)
                @php
                    $statusColor = match(true) {
                        $request->status === 'rejected' => 'stamp',
                        $request->status === 'delivered' => 'seal',
                        in_array($request->status, ['assigned','accepted','picked_up','in_transit']) => 'brass',
                        default => 'envelope',
                    };
                @endphp
                <a href="{{ route('requester.track', $request) }}"
                   class="group flex bg-white rounded-xl border border-ink/10 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="w-1.5 bg-{{ $statusColor }}"></div>

                    <div class="flex-1 flex justify-between items-center p-5">
                        <div>
                            <p class="font-mono text-[11px] text-envelope tracking-wide">#PT-{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="font-display text-lg text-ink mt-0.5">{{ $request->document_type }}</p>
                            <p class="text-sm text-envelope mt-0.5">{{ $request->delivery_address }}</p>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-{{ $statusColor }}/10 text-{{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>

                    <div class="flex items-center px-5" style="border-left: 1.5px dashed #14213D22;">
                        <span class="text-envelope group-hover:text-ink group-hover:translate-x-0.5 transition">→</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection