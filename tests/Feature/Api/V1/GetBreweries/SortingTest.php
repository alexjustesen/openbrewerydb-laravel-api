<?php

use App\Models\Brewery;

test('breweries can be sorted by name ascending', function () {
    createBrewery(['name' => 'Zebra Brewing']);
    createBrewery(['name' => 'Alpha Brewing']);
    createBrewery(['name' => 'Beta Brewing']);

    $response = $this->getJson('/v1/breweries?sort=name:asc');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries->pluck('name')->toArray())->toBe([
        'Alpha Brewing',
        'Beta Brewing',
        'Zebra Brewing'
    ]);
});

test('breweries can be sorted by name descending', function () {
    createBrewery(['name' => 'Zebra Brewing']);
    createBrewery(['name' => 'Alpha Brewing']);
    createBrewery(['name' => 'Beta Brewing']);

    $response = $this->getJson('/v1/breweries?sort=name:desc');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries->pluck('name')->toArray())->toBe([
        'Zebra Brewing',
        'Beta Brewing',
        'Alpha Brewing'
    ]);
});

test('breweries can be sorted by multiple fields', function () {
    createBrewery(['name' => 'Alpha Brewing', 'city' => 'Portland']);
    createBrewery(['name' => 'Alpha Brewing', 'city' => 'Seattle']);
    createBrewery(['name' => 'Beta Brewing', 'city' => 'Portland']);

    $response = $this->getJson('/v1/breweries?sort=name:asc,city:desc');

    $response->assertOk();
    $breweries = collect($response->json());
    $firstTwo = $breweries->take(2)->map(fn($b) => ['name' => $b['name'], 'city' => $b['city']])->toArray();
    expect($firstTwo)->toBe([
        ['name' => 'Alpha Brewing', 'city' => 'Seattle'],
        ['name' => 'Alpha Brewing', 'city' => 'Portland']
    ]);
});
