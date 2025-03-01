<?php

test('random brewery returns a single brewery by default', function () {
    createBreweries(5);

    $response = $this->getJson('/v1/breweries/random');

    $response->assertStatus(200)
        ->assertJsonCount(1);
});

test('random brewery returns the requested number of breweries', function () {
    createBreweries(10);

    $response = $this->getJson('/v1/breweries/random?size=3');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('random brewery validates size parameter', function () {
    createBreweries(5);

    // Test size less than 1
    $response = $this->getJson('/v1/breweries/random?size=0');
    $response->assertStatus(400);

    // Test size greater than 50
    $response = $this->getJson('/v1/breweries/random?size=51');
    $response->assertStatus(400);

    // Test invalid size type
    $response = $this->getJson('/v1/breweries/random?size=abc');
    $response->assertStatus(400);
});

test('random brewery returns all available breweries when size is greater than total', function () {
    createBreweries(3);

    $response = $this->getJson('/v1/breweries/random?size=5');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('random brewery returns empty array when no breweries exist', function () {
    $response = $this->getJson('/v1/breweries/random');

    $response->assertStatus(200)
        ->assertJsonCount(0);
});

test('random brewery returns different breweries on subsequent requests', function () {
    createBreweries(20);

    $response1 = $this->getJson('/v1/breweries/random?size=5');
    $response2 = $this->getJson('/v1/breweries/random?size=5');

    $breweries1 = collect($response1->json())->pluck('id')->sort()->values();
    $breweries2 = collect($response2->json())->pluck('id')->sort()->values();

    // There's a very small chance this could fail randomly, but it's extremely unlikely
    expect($breweries1)->not->toEqual($breweries2);
});

test('random brewery returns complete brewery resource', function () {
    $brewery = createBrewery([
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

    $response = $this->getJson('/v1/breweries/random');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonStructure([[
            'id',
            'name',
            'brewery_type',
            'address_1',
            'city',
            'state_province',
            'postal_code',
            'country',
            'phone',
            'website_url',
        ]]);
});
