<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GuideResource;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GuideController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Guide::query()->where('is_active', true);

        if ($request->filled('min_experience')) {
            $query->where('experience_years', '>=', (int) $request->integer('min_experience'));
        }

        $guides = $query->orderBy('experience_years', 'desc')->get();

        return GuideResource::collection($guides);
    }
}


