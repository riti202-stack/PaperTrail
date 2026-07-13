<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCanAccessRequestChat
{
    public function handle(Request $request, Closure $next)
    {
        $documentRequest = $request->route('document_request');
        $user = $request->user();

        $isRequester = $documentRequest->requester_id === $user->id;
        $isAssignedRunner = $user->runner?->id === $documentRequest->runner_id;

        if (!$isRequester && !$isAssignedRunner) {
            abort(403, 'Not authorized for this request chat.');
        }

        return $next($request);
    }
}