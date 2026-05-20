<?php 

namespace App\Services\Xendit;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

Class XenditService implements XenditServiceInterface {

    public $xenditBaseUrl = 'https://api.xendit.co';

    public function sessionPayment (array $payload)
    {

        $response = Http::withBasicAuth(env('XENDIT_KEY'), '')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->xenditBaseUrl . '/sessions', $payload);

            if ($response->failed()) {
                Log::error('Xendit API request failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                throw new Exception('Failed to create Xendit session. Please try again later.' . $response->body());
            }

            return $response->json();
    }
}