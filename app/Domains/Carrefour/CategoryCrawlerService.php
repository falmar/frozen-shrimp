<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryCrawlOutput;
use App\Libraries\Context\Context;
use GuzzleHttp\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryCrawlerService implements CategoryCrawlerServiceInterface
{
    private array $defaultHeaders = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36',
    ];
    private int $defaultTimeout = 10;

    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * @inheritDoc
     */
    public function crawl(Context $context, CategoryCrawlInput $input): CategoryCrawlOutput
    {
        try {
            $response = $this->client->request(
                'GET',
                $input->url,
                $this->getConfig(
                    headers: $input->headers,
                    timeout: $input->timeout
                )
            );

            $output = new CategoryCrawlOutput();
            $output->content = $response->getContent();
            $output->headers = $response->getHeaders();
            $output->modified = $response->getStatusCode() !== 304;

            return $output;
        } catch (ClientException $exception) {
            // TODO: create exception classes to streamline "crawler" error handling
//            throw new CategoryCrawlerException(
//                "Error crawling category page: {$exception->getMessage()}",
//                $exception->getCode(),
//                $exception,
//            );

            throw $exception;
        }
    }

    private function getConfig(
        array $headers = [],
        int $timeout = 0
    ): array {
        return [
            'headers' => [
                ...$this->defaultHeaders,
                ...$headers,
            ],
            'timeout' => $timeout > 0 ? $timeout : $this->defaultTimeout,
        ];
    }
}
