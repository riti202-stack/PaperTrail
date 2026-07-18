@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6">
    <div class="flex items-baseline justify-between mb-6 pb-4 border-b-2 border-ink/10">
        <div>
            <h2 class="font-display text-2xl text-ink">Manage requests</h2>
            <p class="text-envelope text-sm mt-0.5">Active document courier requests</p>
        </div>
        <a href="{{ route('admin.requests.history') }}" class="text-sm text-envelope hover:text-ink transition">
            View history →
        </a>
    </div>

    <div class="space-y-3">
        @foreach ($requests as $request)
            <div class="bg-white border border-ink/10 rounded-lg p-4 flex justify-between items-start">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center flex-shrink-0
                        @if($request->status === 'rejected') border-stamp text-stamp
                        @elseif(in_array($request->status, ['delivered'])) border-seal text-seal
                        @elseif(in_array($request->status, ['assigned','accepted','picked_up','in_transit'])) border-brass text-brass
                        @else border-envelope/40 text-envelope @endif">
                        <span class="font-mono text-xs font-medium">#{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-ink">{{ $request->document_type }}</p>
                        <p class="text-sm text-envelope">{{ $request->requester->name }}</p>
                        <p class="text-sm text-envelope">{{ $request->pickup_location }} → {{ $request->delivery_address }}</p>
                        <p class="text-xs text-envelope/70 font-mono mt-1">Zone: {{ $request->zone?->name ?? 'N/A' }}</p>
                        <span id="status-badge-{{ $request->id }}" class="inline-block mt-2 text-xs font-medium px-2.5 py-1 rounded-full
                            @if($request->status === 'rejected') bg-stamp/10 text-stamp
                            @elseif($request->status === 'delivered') bg-seal/10 text-seal
                            @elseif(in_array($request->status, ['assigned','accepted','picked_up','in_transit'])) bg-brass/10 text-brass
                            @else bg-envelope/10 text-envelope @endif">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                        @if ($request->runner)
                            <p class="text-sm text-seal mt-1">→ {{ $request->runner->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-2 items-end flex-shrink-0">
                    @if ($request->status === 'requested')
                        <form method="POST" action="{{ route('admin.requests.approve', $request) }}">
                            @csrf
                            <button class="bg-seal text-white text-sm px-3 py-1.5 rounded hover:bg-seal/90 transition">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.requests.reject', $request) }}">
                            @csrf
                            <button class="bg-stamp text-white text-sm px-3 py-1.5 rounded hover:bg-stamp/90 transition">Reject</button>
                        </form>
                    @endif

                    @if ($request->status === 'approved')
                        <button onclick="openAssignModal({{ $request->id }})"
                                class="bg-ink text-white text-sm px-3 py-1.5 rounded hover:bg-ink/90 transition">
                            Assign runner
                        </button>
                    @endif

                    @if (in_array($request->status, ['rejected', 'delivered']))
                        <form method="POST" action="{{ route('admin.requests.archive', $request) }}">
                            @csrf
                            <button class="text-envelope text-sm hover:text-ink transition">Move to history</button>
                        </form>
                    @endif
                </div>
            </div>

            <div id="assign-modal-{{ $request->id }}" class="hidden bg-white border border-ink/10 rounded-lg p-4 -mt-2">
                <form method="POST" action="{{ route('admin.requests.assign', $request) }}" class="flex gap-2 items-center">
                    @csrf
                    <select name="runner_id" id="runner-select-{{ $request->id }}" class="border border-ink/20 rounded px-2 py-1.5 text-sm flex-1">
                        <option value="">Loading eligible runners...</option>
                    </select>
                    <button type="submit" class="bg-ink text-white text-sm px-3 py-1.5 rounded">Confirm</button>
                </form>
            </div>
        @endforeach

        @if ($requests->isEmpty())
            <p class="text-envelope text-center py-12">No active requests.</p>
        @endif
    </div>
</div>

<script>
async function openAssignModal(requestId) {
    const modal = document.getElementById(`assign-modal-${requestId}`);
    modal.classList.toggle('hidden');
    if (modal.classList.contains('hidden')) return;

    try {
        const res = await fetch(`/admin/requests/${requestId}/eligible-runners`);
        const runners = await res.json();
        const select = document.getElementById(`runner-select-${requestId}`);
        select.innerHTML = runners.length === 0
            ? '<option value="">No available runners for this zone</option>'
            : runners.map(r => `<option value="${r.id}">${r.name} — ${r.phone}</option>`).join('');
    } catch (e) { console.error('Failed to load runners:', e); }
}

async function pollAllStatuses() {
    document.querySelectorAll('[id^="status-badge-"]').forEach(async (badge) => {
        const requestId = badge.id.replace('status-badge-', '');
        try {
            const res = await fetch(`/api/requests/${requestId}/status`);
            const data = await res.json();
            badge.textContent = data.status.replace('_', ' ');
        } catch (e) { /* ignore */ }
    });
}
setInterval(pollAllStatuses, 8000);
</script>
@endsection