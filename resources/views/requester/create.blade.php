@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <h2 class="font-display text-2xl text-ink">New request</h2>
        <p class="text-envelope text-sm mt-0.5">Submit a document for courier pickup and delivery</p>
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

    <form method="POST" action="{{ route('requester.store') }}" class="bg-white border border-ink/10 rounded-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Document type</label>
            <input type="text" name="document_type" value="{{ old('document_type') }}"
                   placeholder="e.g. Transcript, Certificate, Admit Card"
                   class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Pickup location</label>
            <input type="text" name="pickup_location" value="{{ old('pickup_location') }}"
                   placeholder="e.g. Registrar Office"
                   class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Delivery address</label>
            <input type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                   class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Pin delivery location</label>
            <div id="pick-map" style="height: 220px;" class="rounded overflow-hidden border border-ink/20"></div>
            <input type="hidden" name="delivery_lat" id="delivery_lat" required>
            <input type="hidden" name="delivery_lng" id="delivery_lng" required>
            <p class="text-xs text-envelope mt-1.5">Click on the map to drop a pin</p>
        </div>

        <div>
            <label class="block text-xs font-mono text-envelope uppercase tracking-wide mb-1.5">Zone</label>
            <select name="zone_id" class="w-full border border-ink/20 rounded px-3 py-2 focus:border-ink focus:ring-0" required>
                <option value="">Select a zone</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full bg-ink text-white px-4 py-2.5 rounded font-medium hover:bg-ink/90 transition">
            Submit request
        </button>
    </form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
const pickMap = L.map('pick-map').setView([22.8456, 89.5403], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(pickMap);
let pickMarker = null;
pickMap.on('click', (e) => {
    const { lat, lng } = e.latlng;
    if (!pickMarker) { pickMarker = L.marker([lat, lng]).addTo(pickMap); } else { pickMarker.setLatLng([lat, lng]); }
    document.getElementById('delivery_lat').value = lat;
    document.getElementById('delivery_lng').value = lng;
});
</script>
@endsection