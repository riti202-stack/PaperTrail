@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">My document requests</h2>
        <a href="{{ route('requester.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
            + New request
        </a>
    </div>

    @if ($requests->isEmpty())
        <p class="text-gray-500">You haven't made any requests yet.</p>
    @else
        <div class="space-y-3">
            @foreach ($requests as $request)
                <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $request->document_type }}</p>
                        <p class="text-sm text-gray-500">{{ $request->delivery_address }}</p>
                        <span class="inline-block mt-1 text-xs px-2 py-1 bg-gray-100 rounded">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>
                    <a href="{{ route('requester.track', $request) }}" class="text-blue-600 text-sm">
                        Track →
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection