<?php

use App\Models\Brewery;
use Tests\Feature\Api\ApiTestCase;

test('returns total breweries', function () {
    // Create known number of breweries
    // Request meta
    // Assert correct total
});

test('returns total breweries by state', function () {
    // Create breweries in different states
    // Request meta
    // Assert correct state totals
});

test('returns total breweries by type', function () {
    // Create breweries of different types
    // Request meta
    // Assert correct type totals
});

test('handles utf8 characters in meta data', function () {
    // Create brewery with UTF-8 characters
    // Request meta
    // Assert proper handling
});
