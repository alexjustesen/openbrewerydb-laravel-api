<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brewery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache as FacadesCache;

class GetBrewery extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $brewery = FacadesCache::remember('brewery_' . $id, 300, function () use ($id) {
            return Brewery::findOrFail($id);
        });

        return response()->json($brewery)
            ->header('Cache-Control', 'public, max-age=300')
            ->setEtag(md5($brewery->updated_at));
    }
}
