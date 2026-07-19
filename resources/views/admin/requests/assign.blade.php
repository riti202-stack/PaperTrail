@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <a href="{{ route('admin.requests.index') }}" class="text-sm text-envelope hover:text-ink transition">← Back to requests</a>
        <h2 class="font-display text-2xl text-ink mt-2">Assign a runner</h2>
        <p class="text-envelope text-sm mt-0.5">
            #PT-{{ str_pad($documentRequest->id, 6, '0', STR_PAD_LEFT) }} — {{ $documentRequest->document_type }}
            → {{ $documentRequest->delivery_address }} <span class="font-mono">({{ $documentRequest->zone?->name }})</span>
        </p>
    </div>

    @if ($runners->isEmpty())
        <div class="text-center py-16">
            <p class="text-envelope">No runners cover this zone yet.</p>
            <a href="{{ route('admin.runners.create') }}" class="text-ink underline text-sm mt-2 inline-block">Add a runner</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($runners as $runner)
                <div class="bg-white border border-ink/10 rounded-lg p-4 flex justify-between items-center">
                    <div class="flex gap-4 items-center">
                        <div class="w-11 h-11 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            {{ $runner->is_available ? 'border-seal text-seal' : 'border-brass text-brass' }}">
                            <span class="font-mono text-xs font-medium">{{ $runner->ongoing_count }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-ink">{{ $runner->name }}</p>
                            <p class="text-sm text-envelope font-mono">{{ $runner->phone }}</p>
                            <div class="flex gap-3 mt-1 text-xs text-envelope">
                                <span><strong class="text-ink">{{ $runner->pending_count }}</strong> pending</span>
                                <span><strong class="text-ink">{{ $runner->ongoing_count }}</strong> ongoing</span>
                                <span><strong class="text-ink">{{ $runner->completed_count }}</strong> completed</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $runner->is_available ? 'bg-seal/10 text-seal' : 'bg-brass/10 text-brass' }}">
                            {{ $runner->is_available ? 'Free' : 'Busy' }}
                        </span>
                        <form method="POST" action="{{ route('admin.requests.assign', $documentRequest) }}">
                            @csrf
                            <input type="hidden" name="runner_id" value="{{ $runner->id }}">
                            <button class="bg-ink text-white text-sm px-3 py-1.5 rounded hover:bg-ink/90 transition">
                                Assign
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-xs text-envelope mt-4">
            Sorted by lowest current workload first. "Busy" runners can still be assigned — useful if a runner declines a task and you need to pick someone else.
        </p>
    @endif
</div>
@endsection