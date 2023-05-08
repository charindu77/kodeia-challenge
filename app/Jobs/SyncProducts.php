<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\WooCommerceService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $page;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $page = 1)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $start = microtime(true);

            $wooCommerceService = new WooCommerceService();
            $response = $wooCommerceService->getProducts($this->page);

            $products = json_decode($response->getBody(), true);

            if (empty($products)) {
                $totalProducts = Product::count();
                Log::info('WooCommerce Sync: Total Products in Shop', ['total_products' => $totalProducts]);
                return;
            }

            foreach ($products as $product) {
                Product::updateOrCreate(
                    ['woo_id' => $product['id']],
                    [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'description' => $product['short_description']
                    ]
                );
            }

            $end = microtime(true);
            $responseTime = $end - $start;

            Log::info('WooCommerce Sync: Response Time', ['response_time' => $responseTime]);
            Log::info('WooCommerce Sync: Number of Products Synced', ['synced_products' => count($products)]);
            
            $delay = $responseTime < 1 ? 60 : 300;
            SyncProducts::dispatch($this->page + 1)->delay($delay);

        } catch (\Exception $e) {
            Log::error('WooCommerce Sync: Error', ['error' => $e->getMessage()]);
        }
    }
}