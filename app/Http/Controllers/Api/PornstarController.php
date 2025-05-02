<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PornstarResource;
use App\Models\Pornstar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PornstarController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Pornstar::query();

        if ($request->has('license')) {
            $query->where('license', $request->license);
        }
        if ($request->has('wl_status')) {
            $query->where('wl_status', $request->boolean('wl_status'));
        }

        $sortField = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_dir', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->get('per_page', 20);
        return PornstarResource::collection($query->paginate($perPage));
    }

    public function show(Pornstar $pornstar): PornstarResource
    {
        return new PornstarResource($pornstar);
    }

    public function licenses(): JsonResponse
    {
        return response()->json([
            'data' => Pornstar::distinct()->pluck('license')->filter()
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total' => Pornstar::count(),
                'by_license' => Pornstar::groupBy('license')
                    ->selectRaw('license, count(*) as count')
                    ->get()
                    ->pluck('count', 'license'),
                'recently_updated' => Pornstar::orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->pluck('name')
            ]
        ]);
    }

    public function search(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $term = $request->query('q');

        $query = Pornstar::query();

        if ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhereJsonContains('aliases', $term);
        }

        $results = $query->paginate(20);

        if ($results->count() === 0) {
            return response()->json([
                'message' => sprintf('No pornstar with alias \'%s\' found', $term)
            ], 400);
        }

        return PornstarResource::collection($query->paginate(20));
    }
}
