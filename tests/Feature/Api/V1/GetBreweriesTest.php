<?php

use App\Models\Brewery;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\ApiTestCase;

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
                    'city',
                    'state_province',
                    'country'
                ]
      ]);
});

test('returns cache control headers', function () {
    Brewery::factory()->create();
    $response = $this->getJson('/v1/breweries');
    $response->assertOk()->assertHeader('Cache-Control', 'max-age=300, public');
});

// Invalid params tests
test('returns default results with invalid params', function () {
    // Make request with invalid params
    // Assert 200 status
    // Assert default 50 breweries
});

// Pagination tests
test('returns another page of breweries', function () {
    // Create 100 breweries
    // Request page 2
    // Assert different breweries than page 1
});

test('returns limited number of breweries', function () {
    // Create 100 breweries
    // Request per_page=20
    // Assert exactly 20 breweries
});

test('does not exceed maximum breweries per page', function () {
    // Create 300 breweries
    // Request per_page=300
    // Assert only 200 breweries returned
});

// City filter tests
test('returns breweries filtered by city', function () {
    // Create breweries with different cities
    // Filter by specific city
    // Assert only matching cities
});

test('returns breweries filtered by multiple cities', function () {
    // Create breweries in different cities
    // Filter by multiple cities
    // Assert matches
});

test('handles plus as space in city filter', function () {
    // Create brewery with "San Diego"
    // Filter by "San+Diego"
    // Assert matches
});

// IDs filter tests
test('returns breweries by ids with limit', function () {
    // Create 60 breweries
    // Request 60 IDs
    // Assert only 50 returned
});

// Country filter tests
test('returns breweries filtered by country', function () {
    // Create breweries in different countries
    // Filter by country
    // Assert matches
});

test('handles plus as space in country filter', function () {
    // Create brewery in "New Zealand"
    // Filter by "New+Zealand"
    // Assert matches
});

// Name filter tests
test('returns breweries filtered by name', function () {
    // Create breweries with different names
    // Filter by name
    // Assert matches
});

test('handles different space formats in name filter', function () {
    // Create brewery with spaces in name
    // Test with +, %20, and actual space
    // Assert all match
});

// State filter tests
test('returns breweries filtered by state', function () {
    // Create breweries in different states
    // Filter by state
    // Assert matches
});

test('returns breweries with snake case state', function () {
    // Create brewery in "New York"
    // Filter by "new_york"
    // Assert matches
});

test('returns empty list with kebab case state', function () {
    // Create brewery in "New York"
    // Filter by "new-york"
    // Assert empty
});

test('handles plus as space in state filter', function () {
    // Create brewery in "New York"
    // Filter by "New+York"
    // Assert matches
});

test('returns empty list with state abbreviation', function () {
    // Create brewery in "California"
    // Filter by "CA"
    // Assert empty
});

test('returns empty list with misspelled state', function () {
    // Create brewery in "California"
    // Filter by "Kalifornia"
    // Assert empty
});

test('returns breweries with utf8 state names', function () {
    // Create brewery with UTF-8 characters
    // Filter by UTF-8 name
    // Assert matches
});

test('sanitizes sql like characters in state filter', function () {
    // Create breweries
    // Test with \ and % characters
    // Assert proper sanitization
});

// Type filter tests
test('returns breweries filtered by type', function () {
    // Create breweries of different types
    // Filter by specific type
    // Assert matches
});

test('returns empty list for invalid brewery type', function () {
    // Create breweries
    // Filter by non-existent type
    // Assert empty
});

test('returns breweries filtered by multiple types', function () {
    // Create breweries of different types
    // Filter by multiple types
    // Assert matches
});
