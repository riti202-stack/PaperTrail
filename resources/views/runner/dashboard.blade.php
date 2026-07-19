@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-6">
    <div class="mb-6 pb-4 border-b-2 border-ink/10">
        <h2 class="font-display text-2xl text-ink">Runner dashboard</h2>
        <p class="text-envelope text-sm mt-0.5">{{ auth()->user()->name }}</p>
    </div>

    @if ($newTasks->isNotEmpty())
        <div class="mb-6">
            <p class="text-xs font-mono uppercase tracking-wide text-brass mb-2">New tasks awaiting response</p>
            <div class="space-y-3">
                @foreach ($newTasks as $task)
                    <div class="bg-brass/5 border border-brass/30 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-mono text-xs text-envelope">#PT-{{ str_pad($task->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="font-medium text-ink mt-0.5">{{ $task->document_type }}</p>
                                <p class="text-sm text-envelope">{{ $task->pickup_location }} → {{ $task->delivery_address }}</p>
                            </div>
                        <!-- </div>
                        <form method="POST" action="{{ route('runner.accept', $task) }}" class="mt-3">
                            @csrf
                            <button class="bg-brass text-white text-sm px-3 py-1.5 rounded hover:bg-brass/90 transition">Accept task</button>
                        </form>
                    </div> -->
                    
                    <div class="flex gap-2 mt-3">
    <form method="POST" action="{{ route('runner.accept', $task) }}">
        @csrf
        <button class="bg-brass text-white text-sm px-3 py-1.5 rounded hover:bg-brass/90 transition">Accept task</button>
    </form>
    <form method="POST" action="{{ route('runner.decline', $task) }}">
        @csrf
        <button class="border border-stamp text-stamp text-sm px-3 py-1.5 rounded hover:bg-stamp/5 transition">Decline</button>
    </form>
</div>

                @endforeach
            </div>
        </div>
    @endif

    <p class="text-xs font-mono uppercase tracking-wide text-envelope mb-2">Ongoing deliveries</p>
    <div class="space-y-2">
        @foreach ($ongoingTasks as $task)
            <a href="{{ route('runner.active', $task) }}" class="block bg-white border border-ink/10 rounded-lg p-4 hover:border-ink/30 transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-mono text-xs text-envelope">#PT-{{ str_pad($task->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-sm text-ink mt-0.5">{{ $task->document_type }}</p>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-brass/10 text-brass">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
            </a>
        @endforeach

        @if ($ongoingTasks->isEmpty() && $newTasks->isEmpty())
            <p class="text-envelope text-center py-12">No tasks right now.</p>
        @endif
    </div>
</div>

<script>
async function checkNewTasks() {
    try {
        const res = await fetch('/api/runner/new-task-count');
        const data = await res.json();
        const badge = document.getElementById('runner-task-badge');
        if (badge) {
            badge.textContent = data.count > 0 ? data.count : '';
            badge.style.display = data.count > 0 ? 'inline-block' : 'none';
        }
    } catch (e) { console.error('Task count fetch failed:', e); }
}
checkNewTasks();
setInterval(checkNewTasks, 10000);
</script>
@endsection