<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportBreweries
{
    use AsAction;

    public function handle()
    {
        DB::raw('TRUNCATE TABLE breweries');

        $json = file_get_contents(
            filename: 'https://raw.githubusercontent.com/openbrewerydb/openbrewerydb/refs/heads/master/breweries.json',
        );

        $data = collect(json_decode(json: $json, associative: true));

        $data
            ->chunk(100)
            ->each(function ($chunk) {
                DB::table('breweries')->insertOrIgnore(
                    $chunk->map(function ($brewery) {
                        return [
                            'id' => $brewery['id'],
                            'name' => $brewery['name'],
                            'brewery_type' => $brewery['brewery_type'],
                            'address_1' => $brewery['address_1'],
                            'address_2' => $brewery['address_2'],
                            'address_3' => $brewery['address_3'],
                            'city' => $brewery['city'],
                            'state_province' => $brewery['state_province'],
                            'country' => $brewery['country'],
                            'postal_code' => $brewery['postal_code'],
                            'website_url' => $brewery['website_url'],
                            'phone' => $brewery['phone'],
                            'latitude' => $brewery['latitude'],
                            'longitude' => $brewery['longitude'],
                        ];
                    })->toArray(),
                );
            });
    }
}
