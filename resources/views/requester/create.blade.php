@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">New document request</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('requester.store') }}" class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Document type</label>
            <input type="text" name="document_type" value="{{ old('document_type') }}"
                   placeholder="e.g. Transcript, Certificate, Admit Card"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Pickup location</label>
            <input type="text" name="pickup_location" value="{{ old('pickup_location') }}"
                   placeholder="e.g. Registrar Office"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Delivery address</label>
            <input type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Zone</label>
            <select name="zone_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select a zone</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Submit request
        </button>
    </form>
</div>
@endsection