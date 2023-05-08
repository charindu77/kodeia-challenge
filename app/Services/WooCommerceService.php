<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class WooCommerceService
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.woocommerce.base_url');
        $this->consumerKey = config('services.woocommerce.consumer_key');
        $this->consumerSecret = config('services.woocommerce.consumer_secret');
    }

    public function getProducts(int $page)
    {
        $client = new Client();

        $url = $this->baseUrl.'/wp-json/wc/v3/products';
        $options = [
            'auth' => [
                $this->consumerKey,
                $this->consumerSecret,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'per_page' => 10,
                'page' => $page,
            ]
        ];

        $response = $client->get($url, $options);
        return $response;
    }

}
