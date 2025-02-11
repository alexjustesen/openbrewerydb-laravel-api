<?php

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\ApiTestCase;

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
    $microBrewery = createBrewery(['brewery_type' => \App\Enums\BreweryType::Micro]);
    $regionalBrewery = createBrewery(['brewery_type' => \App\Enums\BreweryType::Regional]);
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
    $hasCache = Cache::has('brewery_' . $brewery->id);
    expect($hasCache)->toBeTrue();
});

test('response has correct json api structure', function () {
    $brewery = createBrewery();
    $response = $this->getJson("/v1/breweries/{$brewery->id}");
    $this->assertJsonApiStructure($response);
});
