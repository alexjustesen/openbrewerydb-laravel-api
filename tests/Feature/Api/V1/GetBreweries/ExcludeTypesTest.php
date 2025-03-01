<?php

use App\Enums\BreweryType;

test('breweries can be filtered by excluded types', function () {
    createBrewery([
        'name' => 'Micro Brewery',
        'brewery_type' => BreweryType::Micro,
    ]);

    createBrewery([
        'name' => 'Brewpub',
        'brewery_type' => BreweryType::Brewpub,
    ]);

    createBrewery([
        'name' => 'Large Brewery',
        'brewery_type' => BreweryType::Large,
    ]);

    $response = $this->getJson('/v1/breweries?exclude_types=micro,brewpub');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['name'])->toBe('Large Brewery');
});

test('exclude types can be combined with other filters', function () {
    createBrewery([
        'name' => 'Portland Micro',
        'brewery_type' => BreweryType::Micro,
        'city' => 'Portland',
    ]);

    createBrewery([
        'name' => 'Portland Pub',
        'brewery_type' => BreweryType::Brewpub,
        'city' => 'Portland',
    ]);

    createBrewery([
        'name' => 'Seattle Micro',
        'brewery_type' => BreweryType::Micro,
        'city' => 'Seattle',
    ]);

    $response = $this->getJson('/v1/breweries?exclude_types=brewpub&by_city=Portland');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['name'])->toBe('Portland Micro');
});

test('exclude types handles invalid type values', function () {
    createBrewery([
        'name' => 'Micro Brewery',
        'brewery_type' => BreweryType::Micro,
    ]);

    $response = $this->getJson('/v1/breweries?exclude_types=invalid_type');
    $response->assertOk();
    expect($response->json())->toHaveCount(1);
});
