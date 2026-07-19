@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <p class="font-mono text-xs text-envelope">#PT-{{ str_pad($documentRequest->id, 6, '0', STR_PAD_LEFT) }}</p>
        <h2 class="font-display text-2xl text-ink">Delivering to {{ $documentRequest->delivery_address }}</h2>
        <span id="status-badge" class="inline-block mt-2 text-xs font-medium px-2.5 py-1 rounded-full bg-brass/10 text-brass">
            {{ ucfirst(str_replace('_', ' ', $documentRequest->status)) }}
        </span>
    </div>

    <div id="map" style="height: 280px;" class="rounded-lg overflow-hidden border border-ink/10 mb-4"></div>

    <div class="flex gap-2 mb-4">
        <button id="location-toggle" onclick="toggleLocationSharing()"
                class="bg-seal text-white px-4 py-2 rounded text-sm hover:bg-seal/90 transition">
            Start sharing location
        </button>

        @if ($documentRequest->status !== 'delivered')
            <form method="POST" action="{{ route('runner.advance', $documentRequest) }}">
                @csrf
                <button class="bg-ink text-white px-4 py-2 rounded text-sm hover:bg-ink/90 transition">
                    @php
                        $labels = [
                            'accepted' => 'Collected from distribution center',
                            'picked_up' => 'Start delivery',
                            'in_transit' => 'Mark as delivered',
                        ];
                    @endphp
                    {{ $labels[$documentRequest->status] ?? 'Advance status' }}
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white border border-ink/10 rounded-lg overflow-hidden">
        <p class="font-mono text-xs text-envelope px-4 pt-3 pb-2 border-b border-ink/10 tracking-wide">MESSAGES</p>
        <div id="chat-box" style="height: 180px; overflow-y: auto;" class="p-4"></div>
        <div class="flex gap-2 p-3 border-t border-ink/10">
            <input type="text" id="chat-input" placeholder="Message the requester..." class="flex-1 border border-ink/20 rounded px-3 py-1.5 text-sm">
            <button onclick="sendMessage()" class="bg-ink text-white px-4 py-1.5 rounded text-sm hover:bg-ink/90 transition">Send</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
const requestId = {{ $documentRequest->id }};
const currentUserId = {{ auth()->id() }};
let watchId = null;

const map = L.map('map').setView([22.8456, 89.5403], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let myMarker = null;

@if ($documentRequest->delivery_lat && $documentRequest->delivery_lng)
    const destination = [{{ $documentRequest->delivery_lat }}, {{ $documentRequest->delivery_lng }}];
    L.marker(destination).addTo(map).bindPopup('Delivery destination');
    map.setView(destination, 14);
@endif

function toggleLocationSharing() {
    const btn = document.getElementById('location-toggle');
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        btn.textContent = 'Start sharing location';
        btn.classList.replace('bg-stamp', 'bg-seal');
        return;
    }
    if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }

    watchId = navigator.geolocation.watchPosition(
        async (position) => {
            const { latitude, longitude } = position.coords;
            const pos = [latitude, longitude];
            if (!myMarker) { myMarker = L.marker(pos).addTo(map).bindPopup('You'); } else { myMarker.setLatLng(pos); }
            map.panTo(pos);
            try {
                await fetch('/api/runner/location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ lat: latitude, lng: longitude }),
                });
            } catch (e) { console.error('Failed to send location:', e); }
        },
        (error) => console.error('Geolocation error:', error),
        { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
    );
    btn.textContent = 'Stop sharing location';
    btn.classList.replace('bg-seal', 'bg-stamp');
}

async function loadMessages() {
    try {
        const res = await fetch(`/api/requests/${requestId}/chat`);
        const messages = await res.json();
        const box = document.getElementById('chat-box');
        box.innerHTML = messages.map(m => `
            <div style="text-align:${m.sender_id === currentUserId ? 'right' : 'left'}; margin-bottom:8px;">
                <span style="font-size:11px; color:#6B6A65; font-family:'IBM Plex Mono',monospace;">${m.sender.name}</span><br>
                <span style="font-size:14px; color:#14213D;">${m.message}</span>
            </div>
        `).join('');
        box.scrollTop = box.scrollHeight;
    } catch (e) { console.error('Chat fetch failed:', e); }
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    if (!input.value.trim()) return;
    try {
        await fetch(`/api/requests/${requestId}/chat`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ message: input.value }),
        });
        input.value = '';
        await loadMessages();
    } catch (e) { console.error('Send failed:', e); }
}

loadMessages();
setInterval(loadMessages, 4000);
</script>
@endsection