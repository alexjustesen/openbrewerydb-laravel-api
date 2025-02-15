<?php

use App\Models\Brewery;

test('breweries can be filtered by postal code', function () {
    createBrewery([
        'name' => 'Portland Brewery',
        'postal_code' => '97201'
    ]);

    createBrewery([
        'name' => 'Seattle Brewery',
        'postal_code' => '98101'
    ]);

    $response = $this->getJson('/v1/breweries?by_postal=97201');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['name'])->toBe('Portland Brewery');
});

test('postal code filter supports partial matches', function () {
    createBrewery([
        'name' => 'Portland Downtown',
        'postal_code' => '97201'
    ]);

    createBrewery([
        'name' => 'Portland Suburbs',
        'postal_code' => '97229'
    ]);

    createBrewery([
        'name' => 'Seattle Brewery',
        'postal_code' => '98101'
    ]);

    $response = $this->getJson('/v1/breweries?by_postal=972');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(2)
        ->and($breweries->pluck('postal_code')->all())->toContain('97201', '97229');
});

test('postal code filter handles special characters', function () {
    createBrewery([
        'name' => 'Canadian Brewery',
        'postal_code' => 'V6B 1A1'
    ]);

    $response = $this->getJson('/v1/breweries?by_postal=V6B');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(1)
        ->and($breweries->first()['postal_code'])->toBe('V6B 1A1');
});
