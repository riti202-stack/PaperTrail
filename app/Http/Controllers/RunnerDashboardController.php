<?php
namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class RunnerDashboardController extends Controller
{
    public function index()
    {
        $runner = auth()->user()->runner;

        $newTasks = $runner->documentRequests()->where('status', 'assigned')->get();
        $ongoingTasks = $runner->documentRequests()
            ->whereIn('status', ['accepted', 'picked_up', 'in_transit'])
            ->get();

        return view('runner.dashboard', compact('newTasks', 'ongoingTasks'));
    }

    public function active($documentRequestId)
    {
        $documentRequest = auth()->user()->runner->documentRequests()->findOrFail($documentRequestId);
        return view('runner.active', compact('documentRequest'));
    }

    public function acceptTask(DocumentRequest $documentRequest)
    {
        abort_unless($documentRequest->runner_id === auth()->user()->runner->id, 403);
        abort_unless($documentRequest->status === 'assigned', 400);

        $documentRequest->update(['status' => 'accepted']);
        $documentRequest->statusHistory()->create(['status' => 'accepted']);

        return redirect()->route('runner.active', $documentRequest)->with('success', 'Task accepted.');
    }

    public function advanceStatus(Request $request, DocumentRequest $documentRequest)
    {
        abort_unless($documentRequest->runner_id === auth()->user()->runner->id, 403);

        $nextStatus = [
            'accepted' => 'picked_up',
            'picked_up' => 'in_transit',
            'in_transit' => 'delivered',
        ][$documentRequest->status] ?? null;

        abort_if(!$nextStatus, 400, 'No next status available.');

        $documentRequest->update(['status' => $nextStatus]);
        $documentRequest->statusHistory()->create(['status' => $nextStatus]);

        if ($nextStatus === 'delivered') {
            $documentRequest->runner->update(['is_available' => true]);
        }

        return back()->with('success', "Status updated to {$nextStatus}.");
    }

    public function newTaskCount()
    {
        $count = auth()->user()->runner->documentRequests()->where('status', 'assigned')->count();
        return response()->json(['count' => $count]);
    }


    public function declineTask(DocumentRequest $documentRequest)
{
    abort_unless($documentRequest->runner_id === auth()->user()->runner->id, 403);
    abort_unless($documentRequest->status === 'assigned', 400);

    $runner = $documentRequest->runner;

    $documentRequest->update([
        'runner_id' => null,
        'status' => 'approved',
        'assigned_at' => null,
    ]);
    $documentRequest->statusHistory()->create(['status' => 'approved']);

    $runner->update(['is_available' => true]);

    return back()->with('success', 'Task declined. Sent back for reassignment.');
}
}

