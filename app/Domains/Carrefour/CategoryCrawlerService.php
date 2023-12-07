<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryCrawlOutput;
use App\Libraries\Context\Context;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class CategoryCrawlerService implements CategoryCrawlerServiceInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
    ) {
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
                [
                    'headers' => $input->headers,
                    'timeout' => $input->timeout,
                ]
            );

            $output = new CategoryCrawlOutput();
            $output->content = $response->getContent();
            $output->headers = $this->parseHeaders($response->getHeaders());
            $output->modified = $response->getStatusCode() !== 304;

            return $output;
        } catch (TransportExceptionInterface $exception) {
            // TODO: create exception classes to streamline "crawler" error handling
            //  throw new CategoryCrawlerException(
            //      "Error crawling category page: {$exception->getMessage()}",
            //      $exception->getCode(),
            //      $exception,
            //  );

            throw $exception;
        }
    }

    /**
     * This utility method could be moved to a crawler utility trait.
     *
     * @param array<array<string>> $headers
     * @return array<string, string>
     */
    private function parseHeaders(array $headers): array
    {
        $parsedHeaders = [];

        foreach ($headers as $header => $values) {
            $parsedHeaders[$header] = implode('; ', $values);
        }

        return $parsedHeaders;
    }
}
