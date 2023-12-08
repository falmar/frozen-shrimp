<?php

namespace App\Console\Commands;

use App\Domains\Carrefour\CategoryServiceInterface;
use App\Domains\Carrefour\Specs\SaveProductListInput;
use App\Libraries\Context\Context;
use Illuminate\Console\Command;

class SaveProductList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save-product-list {url} {--salepoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        Context $context,
        CategoryServiceInterface $categoryService,
    ): void {
        try {
            // Hyper Hoya de la Plata
            // '005210|4600013||DRIVE|2'

            // Drive Peatón MK Quevedo
            // use as default salepoint
            // '005212|4700003||DRIVE|2'

            $salePoint = $this->option('salepoint') ?: '005212|4700003||DRIVE|2';

            $spec = new SaveProductListInput();
            $spec->url = $this->argument('url');

            if (is_string($salePoint)) {
                $spec->salePoint = $salePoint;
            }

            // process category products given url
            // and save them to database
            $categoryService->saveProductList($context, $spec);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return;
        }
    }
}
