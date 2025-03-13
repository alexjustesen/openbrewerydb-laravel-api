<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BreweryResource;
use App\Models\Brewery;
use App\Rules\BreweryType as BreweryTypeRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ListBreweries extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'per_page' => ['sometimes', 'required', 'integer', 'min:1', 'max:200'],
            'page' => ['integer', 'min:1'],
            'sort' => ['string'],

            // filters
            'by_city' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'by_country' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'by_dist' => ['sometimes', 'required', 'string', 'regex:/^(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)$/'],
            'by_name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'by_postal' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'by_state' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'by_type' => ['sometimes', 'required', 'string', new BreweryTypeRule],
            'by_ids' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'exclude_types' => ['sometimes', 'required', 'string', new BreweryTypeRule],
        ]);

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
                $state = $request->input('by_state');

                // Remove SQL injection vulnerabilities, allow snake_case, kebab-case, and pluses for spaces
                $state = str_replace(['\\', '%', '+', '_', '-'], ['', '', ' ', ' ', ' '], $state);

                $query->whereRaw('LOWER(state_province) LIKE LOWER(?)', ['%'.$state.'%']);
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

                // Validate coordinates
                if (count($values) !== 2) {
                    abort(400, 'Invalid coordinates format');
                }

                if (! is_numeric($values[0]) || $values[0] < -90 || $values[0] > 90) {
                    abort(400, 'Invalid latitude value');
                }

                if (! is_numeric($values[1]) || $values[1] < -180 || $values[1] > 180) {
                    abort(400, 'Invalid longitude value');
                }

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
            ->paginate(perPage: $request->integer('per_page', 50));

        return response()->json(
            BreweryResource::collection($breweries),
            Response::HTTP_OK,
            [
                'Cache-Control' => 'max-age=300, public',
            ]
        );
    }
}
