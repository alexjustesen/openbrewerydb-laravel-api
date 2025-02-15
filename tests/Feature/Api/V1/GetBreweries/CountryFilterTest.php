<?php

use App\Models\Brewery;

test('breweries can be filtered by country', function () {
    createBrewery([
        'name' => 'American Brewery',
        'country' => 'United States'
    ]);

    createBrewery([
        'name' => 'Canadian Brewery',
        'country' => 'Canada'
    ]);

    $response = $this->getJson('/v1/breweries?by_country=Canada');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['name'])->toBe('Canadian Brewery');
});

test('country filter is case insensitive', function () {
    createBrewery([
        'name' => 'Canadian Brewery',
        'country' => 'Canada'
    ]);

    $response = $this->getJson('/v1/breweries?by_country=canada');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['name'])->toBe('Canadian Brewery');
});

test('country filter supports partial matches', function () {
    createBrewery([
        'name' => 'American Brewery',
        'country' => 'United States'
    ]);

    createBrewery([
        'name' => 'British Brewery',
        'country' => 'United Kingdom'
    ]);

    $response = $this->getJson('/v1/breweries?by_country=United');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(2)
        ->and($breweries->pluck('country')->all())->toContain('United States', 'United Kingdom');
});
