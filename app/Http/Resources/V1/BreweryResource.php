<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BreweryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'brewery_type' => $this->brewery_type,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'address_3' => $this->address_3,
            'city' => $this->city,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'phone' => $this->phone,
            'website_url' => $this->website_url,
            'state' => $this->state_province,
            'street' => $this->address_1,
            'distance' => $this->distance ?? null,
        ]);
    }
}
