<?php

namespace App\Http\Controllers\Display;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class BrandController extends Controller
{
    public function fetch()
    {
        $client = new Client(['base_uri' => config('displayqueue.base_url')]);
        $response = $client->get('api/faskes/profile', [
            'headers' => ['Accept' => 'application/json']
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        return response()->json([
            'name' => $data['name'] ?? ''
        ]);
    }
}
