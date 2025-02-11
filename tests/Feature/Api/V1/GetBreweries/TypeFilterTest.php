<?php

namespace Tests\Feature\Api\V1\GetBreweries;

use App\Enums\BreweryType;
use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('returns breweries filtered by type', function () {
    // Create breweries of different types
    $breweries = Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Micro,
    ]);
    Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Brewpub,
    ]);

    // Filter by specific type
    $response = $this->getJson('/v1/breweries?by_type=micro');

    // Assert only matching types
    $response->assertOk()
        ->assertJsonCount(5)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'city',
                'state_province',
                'country',
            ],
        ]);
    $types = collect($response->json())->pluck('brewery_type');
    expect($types->contains('brewpub'))->toBeFalse();
    expect($types->contains('micro'))->toBeTrue();
});

test('returns empty list for invalid brewery type', function () {
    // Create breweries of different types
    $breweries = Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Micro,
    ]);
    Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Brewpub,
    ]);

    // Filter by invalid type
    $response = $this->getJson('/v1/breweries?by_type=invalid');

    // Assert no matches
    $response->assertOk()
        ->assertJsonCount(0);
});

test('returns breweries filtered by multiple types', function () {
    // Create breweries of different types
    $breweries = Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Micro,
    ]);
    Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Large,
    ]);
    Brewery::factory()->count(5)->create([
        'brewery_type' => BreweryType::Brewpub,
    ]);

    // Filter by multiple types
    $response = $this->getJson('/v1/breweries?by_type=micro,large');

    // Assert only matching types
    $response->assertOk()
        ->assertJsonCount(10)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'city',
                'state_province',
                'country',
            ],
        ]);
    $types = collect($response->json())->pluck('brewery_type');
    expect($types->contains('brewpub'))->toBeFalse();
    expect($types->contains('micro'))->toBeTrue();
    expect($types->contains('large'))->toBeTrue();
});
