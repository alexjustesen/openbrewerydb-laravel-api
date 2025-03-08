<?php

namespace Tests\Feature\Api;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    /**
     * Additional headers for API requests.
     */
    protected array $headers = [
        'Accept' => 'application/json',
    ];

    /**
     * Create API test response.
     */
    protected function assertJsonApiResponse(TestResponse $response): TestResponse
    {
        return $response
            ->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Assert that the response has a proper JSON API structure.
     */
    protected function assertJsonApiStructure(TestResponse $response): TestResponse
    {
        return $response->assertJsonStructure([
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
        ]);
    }
}
