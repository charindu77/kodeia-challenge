<?php

namespace App\Console\Commands;

use App\Jobs\SyncProducts;
use Illuminate\Console\Command;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from WooCommerce';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->info('Starting products sync...');

        $progressBar = $this->output->createProgressBar(100);
        $progressBar->start();

        SyncProducts::dispatch();

        $progressBar->advance(100);
        $progressBar->finish();

        $this->info('Products sync completed.');

    }
}