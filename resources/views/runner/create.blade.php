@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Add new runner</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.runners.store') }}" class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email (used for login)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Vehicle number (optional)</label>
            <input type="text" name="vehicle_no" value="{{ old('vehicle_no') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Coverage zones</label>
            <div class="space-y-1">
                @foreach ($zones as $zone)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="zone_ids[]" value="{{ $zone->id }}"
                               {{ in_array($zone->id, old('zone_ids', [])) ? 'checked' : '' }}>
                        {{ $zone->name }}
                    </label>
                @endforeach
            </div>
            @if ($zones->isEmpty())
                <p class="text-sm text-gray-500">No zones exist yet — add zones first.</p>
            @endif
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Create runner account
        </button>
    </form>
</div>
@endsection