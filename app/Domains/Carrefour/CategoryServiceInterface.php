<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\SaveProductListInput;
use App\Domains\Carrefour\Specs\SaveProductListOutput;
use App\Domains\Carrefour\Specs\ShowProductListInput;
use App\Domains\Carrefour\Specs\ShowProductListOutput;
use App\Libraries\Context\Context;

interface CategoryServiceInterface
{
    /**
     * Endpoint to save the product list given a category url
     *
     * @param Context $context
     * @param SaveProductListInput $input
     * @return SaveProductListOutput
     */
    public function saveProductList(Context $context, SaveProductListInput $input): SaveProductListOutput;

    /**
     * Endpoint to show the product list given a category url
     *
     * @param Context $context
     * @param ShowProductListInput $input
     * @return ShowProductListOutput
     */
    public function showProductList(Context $context, ShowProductListInput $input): ShowProductListOutput;
}
