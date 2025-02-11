<?php

namespace Tests\Feature\Api\V1\GetBreweries;

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\ApiTestCase;

beforeEach(function () {
    Cache::flush();
});

test('returns breweries filtered by city', function () {
    // Create breweries with different cities
    $breweries = Brewery::factory()->count(5)->create([
        'city' => 'San Diego'
    ]);
    Brewery::factory()->count(5)->create([
        'city' => 'San Antonio'
    ]);

    // Filter by specific city
    $response = $this->getJson('/v1/breweries?by_city=San+Diego');

    // Assert only matching cities
    $response->assertOk()
        ->assertJsonCount(5)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'city',
                'state_province',
                'country'
            ]
        ]);
    $cities = collect($response->json())->pluck('city');
    expect($cities->contains('San Antonio'))->toBeFalse();
    expect($cities->contains('San Diego'))->toBeTrue();
});

test('returns breweries filtered by multiple cities', function () {
    // Create breweries in different cities
    $breweries = Brewery::factory()->count(5)->create([
        'city' => 'San Diego'
    ]);
    Brewery::factory()->count(5)->create([
        'city' => 'San Antonio'
    ]);
    Brewery::factory()->count(5)->create([
        'city' => 'New York'
    ]);

    // Filter by multiple cities
    $response = $this->getJson('/v1/breweries?by_city=San+Diego,San+Antonio');

    // Assert only matching cities
    $response->assertOk()
        ->assertJsonCount(10)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'city',
                'state_province',
                'country'
            ]
        ]);
    $cities = collect($response->json())->pluck('city');
    expect($cities->contains('New York'))->toBeFalse();
    expect($cities->contains('San Diego'))->toBeTrue();
    expect($cities->contains('San Antonio'))->toBeTrue();
});

test('handles plus as space in city filter', function () {
    // Create brewery in "San Diego"
    Brewery::factory()->create(['city' => 'San Diego']);

    // Test with +, %20, and actual space
    $response = $this->getJson('/v1/breweries?by_city=San+Diego');
    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'city',
                'state_province',
                'country'
            ]
        ]);
});
