<?php

use App\Models\Brewery;

test('breweries can be filtered by ids', function () {
    $brewery1 = createBrewery(['name' => 'First Brewery']);
    $brewery2 = createBrewery(['name' => 'Second Brewery']);
    $brewery3 = createBrewery(['name' => 'Third Brewery']);

    $response = $this->getJson("/v1/breweries?by_ids={$brewery1->id},{$brewery3->id}");

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(2)
        ->and($breweries->pluck('name')->all())->toContain('First Brewery', 'Third Brewery')
        ->and($breweries->pluck('name')->all())->not->toContain('Second Brewery');
});

test('id filter handles whitespace in list', function () {
    $brewery1 = createBrewery(['name' => 'First Brewery']);
    $brewery2 = createBrewery(['name' => 'Second Brewery']);

    $response = $this->getJson("/v1/breweries?by_ids={$brewery1->id} , {$brewery2->id}");

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(2);
});

test('id filter returns empty array for non-existent ids', function () {
    createBrewery(['name' => 'Existing Brewery']);

    $response = $this->getJson('/v1/breweries?by_ids=999999,888888');

    $response->assertOk();
    $breweries = collect($response->json());
    expect($breweries)->toHaveCount(0);
});
