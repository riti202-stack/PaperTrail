<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;

class StatusController extends Controller
{
    public function show(DocumentRequest $documentRequest)
    {
        $eta = null;

        if ($documentRequest->runner?->current_lat && $documentRequest->delivery_lat) {
            $distanceKm = $this->haversineDistance(
                $documentRequest->runner->current_lat,
                $documentRequest->runner->current_lng,
                $documentRequest->delivery_lat,
                $documentRequest->delivery_lng
            );

            $averageSpeedKmh = 20;
            $etaMinutes = round(($distanceKm / $averageSpeedKmh) * 60);

            $eta = [
                'distance_km' => round($distanceKm, 2),
                'eta_minutes' => $etaMinutes,
            ];
        }

        return response()->json([
            'status' => $documentRequest->status,
            'runner' => $documentRequest->runner?->only('name', 'phone'),
            'history' => $documentRequest->statusHistory()->get(),
            'eta' => $eta,
        ]);
    }

    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}