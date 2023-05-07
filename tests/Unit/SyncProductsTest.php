<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Jobs\SyncProducts;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncProductsTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_sync_products_handle()
    {
        // Mock the WooCommerce API
        Http::fake([
            'https://example.com/wp-json/wc/v3/products' => Http::response([
                [
                    'id'=>1,
                    'name' => 'Product 1',
                    'price' => '29.99',
                    'description' => 'Description for product 1',
                ],
                [
                    'id'=>2,
                    'name' => 'Product 2',
                    'price' => '15.99',
                    'description' => 'Description for product 2',
                ],
            ]),
        ]);

        // Mock the Log facade
        Log::shouldReceive('info')
            ->with('WooCommerce Sync: Response Time', Mockery::any())
            ->once();
        Log::shouldReceive('info')
            ->with('WooCommerce Sync: Total Products in Shop', ['total_products' => 2])
            ->once();
        Log::shouldReceive('info')
            ->with('WooRCommerce Sync: Number of Products Synced', ['synced_products' => 2])
            ->once();

        // Run the SyncProducts job
        $job = new SyncProducts();
        $job->handle();

    }
}