<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Testing\TestResponse;

abstract class ApiTestCase extends TestCase
{
    /**
     * Additional headers for API requests.
     *
     * @var array
     */
    protected array $headers = [
        'Accept' => 'application/json',
    ];

    /**
     * Create API test response.
     *
     * @param TestResponse $response
     * @return TestResponse
     */
    protected function assertJsonApiResponse(TestResponse $response): TestResponse
    {
        return $response
            ->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Assert that the response has a proper JSON API structure.
     *
     * @param TestResponse $response
     * @return TestResponse
     */
    protected function assertJsonApiStructure(TestResponse $response): TestResponse
    {
        return $response->assertJsonStructure([
            'id',
            'name',
            'brewery_type',
            'city',
            'state_province',
            'country',
        ]);
    }
}
