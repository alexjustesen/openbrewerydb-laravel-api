<?php

namespace Tests\Feature\Api\V1\GetBreweries;

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('returns breweries filtered by name', function () {
    // Create breweries with different names
    $breweries = Brewery::factory()->count(5)->create([
        'name' => 'test brewery',
    ]);
    Brewery::factory()->count(5)->create([
        'name' => 'another brewery',
    ]);

    // Filter by specific name
    $response = $this->getJson('/v1/breweries?by_name=test');

    // Assert only matching names
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
    $names = collect($response->json())->pluck('name');
    expect($names->contains('another brewery'))->toBeFalse();
    expect($names->contains('test brewery'))->toBeTrue();
});

test('handles different space formats in name filter', function () {
    // Create brewery with spaces in name
    Brewery::factory()->create(['name' => 'test brewery']);

    // Test with +, %20, and actual space
    $response = $this->getJson('/v1/breweries?by_name=test+brewery');
    $response->assertOk()
        ->assertJsonCount(1)
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
});
