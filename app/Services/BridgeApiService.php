<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BridgeApiService
{
    protected $baseUrl;
    protected $token;
    protected $datasetId;

    public function __construct()
    {
        $this->baseUrl   = config('services.bridge.base_url');
        $this->token     = config('services.bridge.token');
        $this->datasetId = config('services.bridge.dataset_id');
    }

    protected function request($endpoint, $params = [])
    {
        return Http::withToken($this->token)
            ->get("{$this->baseUrl}/{$endpoint}", array_merge([
                'dataset_id' => $this->datasetId
            ], $params))
            ->json();
    }

    public function properties($filters = [])
    {
        return $this->request('properties', $filters);
    }

    public function propertyDetails($listingKey)
    {
        return $this->request('properties', [
            'ListingKey' => $listingKey
        ]);
    }
}
