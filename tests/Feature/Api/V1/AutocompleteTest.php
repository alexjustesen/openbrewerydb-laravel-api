<?php

use App\Models\Brewery;

test('autocomplete requires a query parameter', function () {
    $response = $this->getJson('/v1/breweries/autocomplete');
    $response->assertStatus(400);
});

test('autocomplete returns matching breweries', function () {
    createBrewery(['name' => 'Great Notion Brewing']);
    createBrewery(['name' => 'Great Lakes Brewing Co']);
    createBrewery(['name' => 'Not Great Brewery']);

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode('Great'));

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['name' => 'Great Notion Brewing'])
        ->assertJsonFragment(['name' => 'Great Lakes Brewing Co']);
});

test('autocomplete returns empty array when no matches found', function () {
    createBrewery(['name' => 'Some Random Brewery']);

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode('XYZ'));

    $response->assertStatus(200)
        ->assertJsonCount(0);
});

test('autocomplete handles special characters correctly', function () {
    createBrewery(['name' => "O'Connor's Brewing"]);
    createBrewery(['name' => 'Über Brewing Co']);

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode("O'Con"));
    $response->assertStatus(200)
        ->assertJsonFragment(['name' => "O'Connor's Brewing"]);

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode('Über'));
    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Über Brewing Co']);
});

test('autocomplete limits results to 15 breweries', function () {
    // Create 20 breweries with similar names
    for ($i = 1; $i <= 20; $i++) {
        createBrewery(['name' => "Test Brewery {$i}"]);
    }

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode('Test'));

    $response->assertStatus(200)
        ->assertJsonCount(15);
});

test('autocomplete returns only id and name fields', function () {
    createBrewery([
        'name' => 'Test Brewery',
        'brewery_type' => 'micro',
        'address_1' => '123 Main St',
        'city' => 'Portland',
        'state_province' => 'Oregon',
        'postal_code' => '97201',
        'country' => 'United States',
        'phone' => '5551234567',
        'website_url' => 'https://testbrewery.com',
    ]);

    $response = $this->getJson('/v1/breweries/autocomplete?query=' . urlencode('Test'));

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonStructure([
            [
                'id',
                'name'
            ]
        ])
        ->assertJsonMissing([
            'brewery_type',
            'address_1',
            'city',
            'state_province',
            'postal_code',
            'country',
            'phone',
            'website_url'
        ]);
});
