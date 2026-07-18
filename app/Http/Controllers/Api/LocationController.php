<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $runner = $request->user()->runner;

        $runner->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
            'location_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function trackRequest(DocumentRequest $documentRequest)
    {
        if (!$documentRequest->runner) {
            return response()->json(['error' => 'No runner assigned yet'], 404);
        }

        return response()->json([
            'lat' => $documentRequest->runner->current_lat,
            'lng' => $documentRequest->runner->current_lng,
            'updated_at' => $documentRequest->runner->location_updated_at,
            'status' => $documentRequest->status,
        ]);
    }
}