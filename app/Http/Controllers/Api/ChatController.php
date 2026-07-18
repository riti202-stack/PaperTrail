<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(DocumentRequest $documentRequest)
    {
        return response()->json(
            $documentRequest->chatMessages()->with('sender:id,name')->get()
        );
    }

    public function store(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $message = $documentRequest->chatMessages()->create([
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return response()->json($message->load('sender:id,name'), 201);
    }
}