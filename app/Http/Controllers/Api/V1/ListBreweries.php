<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BreweryResource;
use App\Models\Brewery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ListBreweries extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:500',
            'page' => 'integer|min:1',
            'sort' => 'string',

            // filters
            'by_city' => 'string|max:255',
            'by_country' => 'string|max:255',
            'by_dist' => 'string|regex:/^(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)$/',
            'by_name' => 'string|max:255',
            'by_postal' => 'string|max:255',
            'by_state' => 'string|max:255',
            'by_type' => 'string|max:100',
            'by_ids' => 'string|max:255',
            'exclude_types' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $breweries = Brewery::query()
            ->select('*')
            ->when($request->has('by_city'), function ($query) use ($request) {
                $cityArray = array_map('trim', explode(',', $request->input('by_city')));
                $query->where(function ($q) use ($cityArray) {
                    foreach ($cityArray as $city) {
                        $q->orWhere('city', 'like', '%'.$city.'%');
                    }
                });
            })
            ->when($request->has('by_country'), function ($query) use ($request) {
                $query->where('country', 'like', '%'.Str::trim($request->input('by_country')).'%');
            })
            ->when($request->has('by_name'), function ($query) use ($request) {
                $name = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], Str::trim($request->input('by_name')));
                $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($name).'%']);
            })
            ->when($request->has('by_postal'), function ($query) use ($request) {
                $query->where('postal_code', 'like', '%'.Str::trim($request->input('by_postal')).'%');
            })
            ->when($request->has('by_state'), function ($query) use ($request) {
                $query->where('state_province', 'like', '%'.Str::trim($request->input('by_state')).'%');
            })
            ->when($request->has('by_type'), function ($query) use ($request) {
                $query->byType($request->input('by_type'));
            })
            ->when($request->has('by_ids'), function ($query) use ($request) {
                $values = explode(',', $request->input('by_ids'));

                $values = collect($values)
                    ->map(function ($value) {
                        return Str::trim($value);
                    })
                    ->take(50)
                    ->toArray();

                $query->whereIn('id', $values);
            })
            ->when($request->has('by_dist'), function ($query) use ($request) {
                $values = explode(',', $request->input('by_dist'));

                $values = collect($values)
                    ->map(function ($value) {
                        return Str::trim($value);
                    })
                    ->toArray();

                $query->orderByDistance($values[0], $values[1]);
            })
            ->when($request->has('exclude_types'), function ($query) use ($request) {
                $values = explode(',', $request->input('exclude_types'));

                $values = collect($values)
                    ->map(function ($value) {
                        return Str::trim($value);
                    })
                    ->toArray();

                $query->whereNotIn('brewery_type', $values);
            })
            ->when($request->has('sort'), function ($query) use ($request) {
                $values = explode(',', $request->input('sort'));

                $values = collect($values)
                    ->map(function ($value) {
                        return explode(':', $value);
                    })
                    ->toArray();

                foreach ($values as $value) {
                    $query->orderBy($value[0], $value[1] ?? 'asc');
                }
            })
            ->orderBy('id')
            ->when(
                $request->has('per_page') && $request->input('per_page') > 200,
                function ($query) {
                    return $query->paginate(
                        perPage: 200,
                        columns: ['*'],
                        pageName: 'page'
                    );
                },
                function ($query) use ($request) {
                    return $query->paginate(
                        perPage: $request->input('per_page', 50),
                        columns: ['*'],
                        pageName: 'page'
                    );
                }
            );

        return response()->json(
            BreweryResource::collection($breweries),
            Response::HTTP_OK,
            [
                'Cache-Control' => 'max-age=300, public',
            ]
        );
    }
}
