<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHuntingBookingRequest;
use App\Http\Resources\HuntingBookingResource;
use App\Models\Guide;
use App\Models\HuntingBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class HuntingBookingController extends Controller
{
    public function store(StoreHuntingBookingRequest $request)
    {
        $validated = $request->validated();

        $guide = Guide::query()
            ->whereKey($validated['guide_id'])
            ->where('is_active', true)
            ->first();

        if (!$guide) {
            return response()->json([
                'message' => 'Guide not found or inactive',
            ], 404);
        }

        $exists = HuntingBooking::query()
            ->where('guide_id', $validated['guide_id'])
            ->where('date', $validated['date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Guide already booked for this date',
                'errors' => [
                    'date' => ['Selected guide has another booking on this date.'],
                ],
            ], 422);
        }

        $booking = HuntingBooking::create($validated);

        return (new HuntingBookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }
}


