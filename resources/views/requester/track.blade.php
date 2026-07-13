@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Tracking request #{{ $documentRequest->id }}</h2>

    <div id="status-badge" class="inline-block px-3 py-1 rounded bg-gray-100 mb-4">
        {{ ucfirst(str_replace('_', ' ', $documentRequest->status)) }}
    </div>

    <div style="position: relative;" class="mb-6">
        <div id="map" style="height: 350px; border-radius: 8px;"></div>

        <div id="map-placeholder" style="
            position: absolute; inset: 0; border-radius: 8px;
            background: rgba(255,255,255,0.92); display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 20px; z-index: 500;">
            <div style="font-size: 28px; margin-bottom: 8px;">📍</div>
            <p id="placeholder-text" style="font-weight: 500; margin: 0 0 4px;">
                Waiting for runner to be assigned
            </p>
            <p style="font-size: 13px; color: #666; margin: 0;">
                The map will update automatically once tracking begins
            </p>
        </div>
    </div>

    <div id="chat-box" style="height: 250px; overflow-y: auto;" class="border rounded p-3 mb-2"></div>
    <div class="flex gap-2">
        <input type="text" id="chat-input" placeholder="Type a message..." class="flex-1 border rounded px-2">
        <button onclick="sendMessage()" class="bg-blue-600 text-white px-4 py-1 rounded">Send</button>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
const requestId = {{ $documentRequest->id }};
const currentUserId = {{ auth()->id() }};

const map = L.map('map').setView([22.8456, 89.5403], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let marker = null;

function hidePlaceholder() {
    document.getElementById('map-placeholder').style.display = 'none';
}
function showPlaceholder(message) {
    document.getElementById('placeholder-text').textContent = message;
    document.getElementById('map-placeholder').style.display = 'flex';
}

async function updateLocation() {
    try {
        const res = await fetch(`/api/requests/${requestId}/location`);
        const data = await res.json();

        if (data.error) {
            showPlaceholder('Waiting for runner to be assigned');
            return;
        }
        if (!data.lat || !data.lng) {
            showPlaceholder('Waiting for runner to start delivery');
            return;
        }

        document.getElementById('status-badge').textContent = data.status.replace('_', ' ');
        const pos = [data.lat, data.lng];
        if (!marker) { marker = L.marker(pos).addTo(map); } else { marker.setLatLng(pos); }
        map.setView(pos, 15);
        hidePlaceholder();
    } catch (e) {
        console.error('Location fetch failed:', e);
        showPlaceholder('Unable to load location right now');
    }
}

async function loadMessages() {
    try {
        const res = await fetch(`/api/requests/${requestId}/chat`);
        const messages = await res.json();
        const box = document.getElementById('chat-box');
        box.innerHTML = messages.map(m => `
            <div style="text-align:${m.sender_id === currentUserId ? 'right' : 'left'}; margin-bottom:6px;">
                <strong>${m.sender.name}:</strong> ${m.message}
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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message: input.value }),
        });
        input.value = '';
        await loadMessages();
    } catch (e) { console.error('Send failed:', e); }
}

updateLocation();
loadMessages();
setInterval(updateLocation, 5000);
setInterval(loadMessages, 4000);
</script>
@endsection