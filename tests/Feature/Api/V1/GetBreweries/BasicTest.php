<?php

namespace Tests\Feature\Api\V1\GetBreweries;

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('returns default number of breweries (50)', function () {
    Brewery::factory()->count(60)->create();
    $response = $this->getJson('/v1/breweries');
    $response->assertOk()
        ->assertJsonCount(50)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'brewery_type',
                'address_1',
                'address_2',
                'address_3',
                'city',
                'state_province',
                'postal_code',
                'country',
                'longitude',
                'latitude',
                'phone',
                'website_url',
                'state',
                'street',
            ],
        ]);
});

test('returns cache control headers', function () {
    Brewery::factory()->create();
    $response = $this->getJson('/v1/breweries');
    $response->assertOk()->assertHeader('Cache-Control', 'max-age=300, public');
});

test('returns default results with invalid params', function () {
    // Create breweries to test with
    Brewery::factory()->count(60)->create();

    // Test with various invalid parameters
    $response = $this->getJson('/v1/breweries?'.http_build_query([
        'per_page' => 'invalid',  // Should be integer
        'sort' => ['invalid:sort'], // Should be string
        'by_city' => str_repeat('x', 256), // Exceeds max length
        'by_type' => str_repeat('x', 101), // Exceeds max length
        'by_state' => ['invalid', 'array'], // Should be string
    ]));

    $response->assertStatus(400);
});
