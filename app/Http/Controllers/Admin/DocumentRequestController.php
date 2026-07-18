<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Runner;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $requests = DocumentRequest::with(['requester', 'runner', 'zone'])
            ->whereNull('archived_at')
            ->latest()
            ->get();

        return view('admin.requests.index', compact('requests'));
    }

    public function history()
    {
        $requests = DocumentRequest::with(['requester', 'runner', 'zone'])
            ->whereNotNull('archived_at')
            ->latest('archived_at')
            ->get();

        return view('admin.requests.history', compact('requests'));
    }

    public function archive(DocumentRequest $documentRequest)
    {
        abort_unless(in_array($documentRequest->status, ['rejected', 'delivered']), 400);

        $documentRequest->update(['archived_at' => now()]);

        return back()->with('success', 'Moved to history.');
    }

    public function approve(DocumentRequest $documentRequest)
    {
        $documentRequest->update(['status' => 'approved']);
        $documentRequest->statusHistory()->create(['status' => 'approved']);
        return back()->with('success', 'Request approved.');
    }

    public function reject(DocumentRequest $documentRequest)
    {
        $documentRequest->update(['status' => 'rejected']);
        $documentRequest->statusHistory()->create(['status' => 'rejected']);
        return back()->with('success', 'Request rejected.');
    }

    public function assignRunner(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate(['runner_id' => 'required|exists:runners,id']);
        $runner = Runner::findOrFail($request->runner_id);

        $documentRequest->update([
            'runner_id' => $runner->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $runner->update(['is_available' => false]);
        $documentRequest->statusHistory()->create(['status' => 'assigned']);

        return back()->with('success', 'Runner assigned.');
    }

    public function eligibleRunners(DocumentRequest $documentRequest)
    {
        $runners = Runner::where('is_available', true)
            ->whereHas('zones', fn($q) => $q->where('zones.id', $documentRequest->zone_id))
            ->get();

        return response()->json($runners);
    }
}