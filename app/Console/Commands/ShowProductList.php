<?php

namespace App\Console\Commands;

use App\Domains\Carrefour\CategoryCrawlerService;
use App\Domains\Carrefour\CategoryParserService;
use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Libraries\Context\Context;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShowProductList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-product-list {url} {--salepoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Context $context)
    {
        try {
            // Hyper Hoya de la Plata
            // '005210|4600013||DRIVE|2'

            // Drive Peatón MK Quevedo
            // '005212|4700003||DRIVE|2'

            $salePoint = $this->option('salepoint') ?: '005212|4700003||DRIVE|2';
            $host = parse_url($this->argument('url'), PHP_URL_HOST);

            $crawler = new CategoryCrawlerService($this->getClient());
            $parser = new CategoryParserService();

            $crawlerSpec = new CategoryCrawlInput();
            $crawlerSpec->url = $this->argument('url');
            $crawlerSpec->headers = [
                'Cookie' => "salepoint={$salePoint}; Domain={$host}; Path=/; SameSite=None",
            ];

            $crawlerOutput = $crawler->crawl($context, $crawlerSpec);

            $parserSpec = new CategoryProductParserInput();
            $parserSpec->url = $crawlerSpec->url;
            $parserSpec->content = $crawlerOutput->content;

            $parserOutput = $parser->products($context, $parserSpec);

            $this->output->writeln(json_encode($parserOutput->products, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return;
        }
    }

    private HttpClientInterface $client;

    private function getClient(): HttpClientInterface
    {
        if (!isset($this->client)) {
            $this->client = HttpClient::create();
        }

        return $this->client;
    }
}
