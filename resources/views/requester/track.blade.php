@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <div class="mb-6">
        <p class="font-mono text-xs text-envelope tracking-wide">TRACKING NUMBER</p>
        <h2 class="font-display text-3xl text-ink">#PT-{{ str_pad($documentRequest->id, 6, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <div id="stepper" class="bg-white border border-ink/10 rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start" id="stepper-track"></div>
        <p id="eta-text" class="font-mono text-xs text-envelope mt-5 text-center"></p>
    </div>

    <div style="position: relative;" class="mb-6 rounded-lg overflow-hidden border border-ink/10">
        <div id="map" style="height: 350px;"></div>
        <div id="map-placeholder" style="
            position: absolute; inset: 0; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 20px; z-index: 500;" class="bg-paper/95">
            <div class="w-10 h-10 rounded-full border-2 border-brass flex items-center justify-center mb-3">
                <span class="text-brass text-lg">✎</span>
            </div>
            <p id="placeholder-text" class="font-display text-ink mb-1">Waiting for admin approval</p>
            <p class="text-xs text-envelope">This updates automatically</p>
        </div>
    </div>

    <div class="bg-white border border-ink/10 rounded-lg overflow-hidden">
        <p class="font-mono text-xs text-envelope px-4 pt-3 pb-2 border-b border-ink/10 tracking-wide">MESSAGES</p>
        <div id="chat-box" style="height: 220px; overflow-y: auto;" class="p-4"></div>
        <div class="flex gap-2 p-3 border-t border-ink/10">
            <input type="text" id="chat-input" placeholder="Type a message..." class="flex-1 border border-ink/20 rounded px-3 py-1.5 text-sm">
            <button onclick="sendMessage()" class="bg-ink text-white px-4 py-1.5 rounded text-sm hover:bg-ink/90 transition">Send</button>
        </div>
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

const STEPS = ['requested', 'approved', 'assigned', 'accepted', 'picked_up', 'in_transit', 'delivered'];
const STEP_LABELS = {
    requested: 'Requested', approved: 'Approved', assigned: 'Assigned', accepted: 'Accepted',
    picked_up: 'Picked up', in_transit: 'In transit', delivered: 'Delivered'
};

function renderStepper(currentStatus) {
    if (currentStatus === 'rejected') {
        document.getElementById('stepper-track').innerHTML =
            '<p class="text-stamp text-center w-full font-display">This request was rejected</p>';
        return;
    }
    const currentIndex = STEPS.indexOf(currentStatus);
    document.getElementById('stepper-track').innerHTML = STEPS.map((step, i) => {
        const done = i <= currentIndex;
        const isCurrent = i === currentIndex;
        return `
            <div style="display:flex; flex-direction:column; align-items:center; flex:1;">
                <div style="width:${isCurrent ? '18px' : '14px'}; height:${isCurrent ? '18px' : '14px'};
                    border-radius:50%; background:${done ? '#3A6B5C' : '#FDFBF7'};
                    border: 2px solid ${done ? '#3A6B5C' : '#6B6A6540'};"></div>
                <p style="font-size:10.5px; margin-top:7px; text-align:center; color:${done ? '#14213D' : '#6B6A65'}; font-family: 'IBM Plex Mono', monospace;">${STEP_LABELS[step]}</p>
            </div>
            ${i < STEPS.length - 1 ? `<div style="flex:1; height:2px; background:${i < currentIndex ? '#3A6B5C' : '#6B6A6525'}; margin-top:8px;"></div>` : ''}
        `;
    }).join('');
}

function hidePlaceholder() { document.getElementById('map-placeholder').style.display = 'none'; }
function showPlaceholder(msg) {
    document.getElementById('placeholder-text').textContent = msg;
    document.getElementById('map-placeholder').style.display = 'flex';
}

async function pollStatus() {
    try {
        const res = await fetch(`/api/requests/${requestId}/status`);
        const data = await res.json();
        renderStepper(data.status);

        const waitingMessages = {
            requested: 'Waiting for admin approval',
            approved: 'Waiting for a runner to be assigned',
            rejected: 'This request was rejected',
            assigned: 'Waiting for runner to accept the task',
            accepted: 'Runner is heading to collect your document',
        };
        if (marker === null && waitingMessages[data.status]) showPlaceholder(waitingMessages[data.status]);

        document.getElementById('eta-text').textContent = data.eta
            ? `EST. ARRIVAL ~${data.eta.eta_minutes} MIN · ${data.eta.distance_km} KM AWAY`
            : '';
    } catch (e) { console.error('Status fetch failed:', e); }
}

async function updateLocation() {
    try {
        const res = await fetch(`/api/requests/${requestId}/location`);
        const data = await res.json();
        if (data.error || !data.lat || !data.lng) return;
        const pos = [data.lat, data.lng];
        if (!marker) { marker = L.marker(pos).addTo(map); } else { marker.setLatLng(pos); }
        map.setView(pos, 15);
        hidePlaceholder();
    } catch (e) { console.error('Location fetch failed:', e); }
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

pollStatus(); updateLocation(); loadMessages();
setInterval(pollStatus, 5000);
setInterval(updateLocation, 5000);
setInterval(loadMessages, 4000);
</script>
@endsection