<?php

namespace Tests\Feature\Api\V1\GetBreweries;

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\ApiTestCase;

beforeEach(function () {
    Cache::flush();
});

test('returns breweries filtered by state', function () {
    // Create breweries in different states
    $breweries = Brewery::factory()->count(5)->create([
        'state_province' => 'California'
    ]);
    Brewery::factory()->count(5)->create([
        'state_province' => 'Texas'
    ]);

    // Filter by specific state
    $response = $this->getJson('/v1/breweries?by_state=California');

    // Assert matches
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
    $states = collect($response->json())->pluck('state_province');
    expect($states->contains('Texas'))->toBeFalse();
    expect($states->contains('California'))->toBeTrue();
});

test('returns breweries with snake case state', function () {
    // Create brewery in "New York"
    Brewery::factory()->create(['state_province' => 'New York']);
    Brewery::factory()->create(['state_province' => 'Boston']);

    // Filter by snake case
    $response = $this->getJson('/v1/breweries?by_state=new_york');

    // Assert matches
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

test('returns list with kebab case state', function () {
    // Create brewery in "New York"
    Brewery::factory()->create(['state_province' => 'New York']);
    Brewery::factory()->create(['state_province' => 'Boston']);

    // Filter by kebab case
    $response = $this->getJson('/v1/breweries?by_state=new-york');

    // Assert no matches
    $response->assertOk()
        ->assertJsonCount(1);
});

test('handles plus as space in state filter', function () {
    // Create brewery in "New York"
    Brewery::factory()->create(['state_province' => 'New York']);
    Brewery::factory()->create(['state_province' => 'Boston']);

    // Test with +, %20, and actual space
    $response = $this->getJson('/v1/breweries?by_state=New+York');

    // Assert matches
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

test('returns empty list with state abbreviation', function () {
    // Create brewery in "California"
    Brewery::factory()->create(['state_province' => 'Texas']);
    Brewery::factory()->create(['state_province' => 'California']);

    // Filter by abbreviation
    $response = $this->getJson('/v1/breweries?by_state=TX');

    // Assert no matches
    $response->assertOk()
        ->assertJsonCount(0);
});

test('returns empty list with misspelled state', function () {
    // Create brewery in "California"
    Brewery::factory()->create(['state_province' => 'California']);
    Brewery::factory()->create(['state_province' => 'Texas']);

    // Filter by misspelling
    $response = $this->getJson('/v1/breweries?by_state=Calfornia');

    // Assert no matches
    $response->assertOk()
        ->assertJsonCount(0);
});

test('returns breweries with utf8 state names', function () {
    // Create brewery in "Köln"
    Brewery::factory()->create(['state_province' => 'Köln']);

    // Filter by utf8 name
    $response = $this->getJson('/v1/breweries?by_state=Köln');

    // Assert matches
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

test('sanitizes sql like characters in state filter', function () {
    // Create brewery in "California"
    Brewery::factory()->create(['state_province' => 'California']);

    // Filter with SQL LIKE characters
    $response = $this->getJson('/v1/breweries?by_state=Cal\%ifornia');

    // Assert matches
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
