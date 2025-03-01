<?php

use App\Enums\BreweryType;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('can get a brewery by id', function () {
    $brewery = createBrewery();
    $response = $this->getJson("/v1/breweries/{$brewery->id}");
    $this->assertJsonApiResponse($response)
        ->assertOk()
        ->assertJsonPath('id', $brewery->id)
        ->assertJsonPath('name', $brewery->name)
        ->assertJsonPath('brewery_type', $brewery->brewery_type->value);
    expect($brewery)->toBeBrewery();
});

test('can get different types of breweries', function () {
    $microBrewery = createBrewery(['brewery_type' => BreweryType::Micro]);
    $regionalBrewery = createBrewery(['brewery_type' => BreweryType::Regional]);
    $response = $this->getJson("/v1/breweries/{$microBrewery->id}");
    expect($microBrewery)->toHaveBreweryType('micro');
    $this->assertJsonApiResponse($response)->assertOk();
    $response = $this->getJson("/v1/breweries/{$regionalBrewery->id}");
    expect($regionalBrewery)->toHaveBreweryType('regional');
    $this->assertJsonApiResponse($response)->assertOk();
});

test('returns 404 for non-existent brewery', function () {
    $response = $this->getJson('/v1/breweries/999999');
    $response->assertNotFound();
});

test('brewery is cached after first request', function () {
    $brewery = createBrewery();
    $response = $this->getJson("/v1/breweries/{$brewery->id}");
    $hasCache = Cache::has('brewery_'.$brewery->id);
    expect($hasCache)->toBeTrue();
});

test('response has correct json api structure', function () {
    $brewery = createBrewery();
    $response = $this->getJson("/v1/breweries/{$brewery->id}");
    $this->assertJsonApiStructure($response);
});

test('returns all brewery fields', function () {
    $brewery = createBrewery([
        'name' => 'Test Brewery',
        'brewery_type' => BreweryType::Micro,
        'address_1' => '123 Main St',
        'address_2' => 'Suite 101',
        'address_3' => 'Floor 2',
        'city' => 'Portland',
        'state_province' => 'Oregon',
        'postal_code' => '97201',
        'country' => 'United States',
        'longitude' => '-122.681944',
        'latitude' => '45.520833',
        'phone' => '5551234567',
        'website_url' => 'https://testbrewery.com',
    ]);

    $response = $this->getJson("/v1/breweries/{$brewery->id}");

    $response->assertOk()
        ->assertJsonStructure([
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
        ]);
});

test('invalid brewery id format returns 404', function () {
    $response = $this->getJson('/v1/breweries/invalid-id');
    $response->assertNotFound();
});
