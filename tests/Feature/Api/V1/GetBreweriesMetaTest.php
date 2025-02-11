<?php

use App\Models\Brewery;
use Tests\Feature\Api\ApiTestCase;
use App\Enums\BreweryType;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('returns total breweries', function () {
    $breweries = Brewery::factory()->count(5)->create();
    
    $response = $this->getJson('/v1/breweries/meta');
    
    $this->assertJsonApiResponse($response)
        ->assertOk()
        ->assertJsonPath('total', 5);
});

test('returns total breweries by state', function () {
    Brewery::factory()->count(3)->state(['state_province' => 'California'])->create();
    Brewery::factory()->count(2)->state(['state_province' => 'Oregon'])->create();
    
    $response = $this->getJson('/v1/breweries/meta');
    
    $this->assertJsonApiResponse($response)
        ->assertOk()
        ->assertJsonPath('by_state.California', 3)
        ->assertJsonPath('by_state.Oregon', 2);
});

test('returns total breweries by type', function () {
    Brewery::factory()->count(2)->state(['brewery_type' => BreweryType::Micro])->create();
    Brewery::factory()->count(1)->state(['brewery_type' => BreweryType::Regional])->create();
    
    $response = $this->getJson('/v1/breweries/meta');
    
    $this->assertJsonApiResponse($response)
        ->assertOk()
        ->assertJsonPath('by_type.micro', 2)
        ->assertJsonPath('by_type.regional', 1);
});

test('handles utf8 characters in meta data', function () {
    Brewery::factory()->count(2)->state(['state_province' => 'São Paulo'])->create();
    
    $response = $this->getJson('/v1/breweries/meta');
    
    $this->assertJsonApiResponse($response)
        ->assertOk()
        ->assertJsonPath('by_state.São Paulo', 2);
});
