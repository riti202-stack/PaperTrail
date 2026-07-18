<?php
namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\Zone;
use Illuminate\Http\Request;

class RequesterController extends Controller
{
    public function index()
    {
        $requests = auth()->user()->documentRequests()->latest()->get();
        return view('requester.index', compact('requests'));
    }

    public function create()
    {
        $zones = Zone::all();
        return view('requester.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'delivery_lat' => 'required|numeric|between:-90,90',
            'delivery_lng' => 'required|numeric|between:-180,180',
        ]);

        $documentRequest = DocumentRequest::create([
            ...$validated,
            'requester_id' => auth()->id(),
            'status' => 'requested',
        ]);

        $documentRequest->statusHistory()->create(['status' => 'requested']);

        return redirect()->route('requester.index')->with('success', 'Request submitted.');
    }

    public function track(DocumentRequest $documentRequest)
    {
        abort_unless($documentRequest->requester_id === auth()->id(), 403);
        return view('requester.track', compact('documentRequest'));
    }
}