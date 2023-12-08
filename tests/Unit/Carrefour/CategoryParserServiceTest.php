<?php

namespace Tests\Unit\Carrefour;

use App\Domains\Carrefour\CategoryParserService;
use App\Domains\Carrefour\CategoryParserServiceInterface;
use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Libraries\Context\AppContext;
use Tests\TestCase;

/**
 * @covers \App\Domains\Carrefour\CategoryParserService
 */
class CategoryParserServiceTest extends TestCase
{
    public function testParse_should_return_no_items(): void
    {
        $context = AppContext::background();
        $parser = $this->getParser();

        $productSpec = new CategoryProductParserInput();
        $productSpec->baseURI = 'https://www.carrefour.es';
        $productSpec->content = $this->getContentEmpty();

        $output = $parser->products($context, $productSpec);

        $this->assertIsArray($output->products);
        $this->assertCount(0, $output->products);
    }

    public function testParse_should_return_items(): void
    {
        $context = AppContext::background();
        $parser = $this->getParser();

        $productSpec = new CategoryProductParserInput();
        $productSpec->baseURI = 'https://www.carrefour.es';
        $productSpec->content = $this->getContentItems();

        $output = $parser->products($context, $productSpec);

        $this->assertIsArray($output->products);
        $this->assertCount(6, $output->products);
    }

    public function testParse_should_return_items_with_correct_attributes(): void
    {
        $context = AppContext::background();
        $parser = $this->getParser();

        $productSpec = new CategoryProductParserInput();
        $productSpec->baseURI = 'https://www.carrefour.es';
        $productSpec->content = $this->getContentItems();

        $output = $parser->products($context, $productSpec);

        $this->assertIsArray($output->products);
        $this->assertCount(6, $output->products);

        foreach ($output->products as $product) {
            $this->assertNotEmpty($product->name);
            $this->assertNotEmpty($product->url);
            $this->assertNotEmpty($product->price);
            $this->assertNotEmpty($product->imageURL);

            $this->assertIsString($product->name);
            $this->assertIsString($product->url);
            $this->assertIsString($product->price);
            $this->assertIsString($product->imageURL);
        }
    }

    public function testParse_should_return_items_with_scheme_host_url(): void
    {
        $context = AppContext::background();
        $parser = $this->getParser();

        $productSpec = new CategoryProductParserInput();
        $productSpec->baseURI = 'https://www.carrefour.es';
        $productSpec->content = $this->getContentItems();

        $output = $parser->products($context, $productSpec);

        $this->assertIsArray($output->products);
        $this->assertCount(6, $output->products);

        foreach ($output->products as $product) {
            $this->assertStringContainsString('https://www.carrefour.es', $product->url);
        }
    }

    public function testParse_should_filter_out_bad_items(): void
    {
        $context = AppContext::background();
        $parser = $this->getParser();

        $productSpec = new CategoryProductParserInput();
        $productSpec->baseURI = 'https://www.carrefour.es';
        $productSpec->content = $this->getContentBadItems();

        $output = $parser->products($context, $productSpec);

        $this->assertIsArray($output->products);
        $this->assertCount(4, $output->products);
    }

    private function getParser(): CategoryParserServiceInterface
    {
        return new CategoryParserService();
    }

    private string $contentItems;

    private function getContentItems(): string
    {
        if (!isset($this->contentItems)) {
            $content = file_get_contents(
                'resources/tests/carrefour/categories/congelados_items.html'
            );

            if ($content === false) {
                throw new \Exception('Error reading file');
            }

            $this->contentItems = $content;
        }

        return $this->contentItems;
    }

    private string $contentBadItems;

    private function getContentBadItems(): string
    {
        if (!isset($this->contentItems)) {
            $content = file_get_contents(
                'resources/tests/carrefour/categories/congelados_items_bad.html'
            );

            if ($content === false) {
                throw new \Exception('Error reading file');
            }

            $this->contentBadItems = $content;
        }

        return $this->contentBadItems;
    }

    private string $contentEmpty;

    private function getContentEmpty(): string
    {
        if (!isset($this->contentEmpty)) {
            $content = file_get_contents(
                'resources/tests/carrefour/categories/congelados_empty.html'
            );

            if ($content === false) {
                throw new \Exception('Error reading file');
            }

            $this->contentEmpty = $content;
        }

        return $this->contentEmpty;
    }
}
