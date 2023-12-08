<?php

namespace Tests\Unit\Carrefour;

use App\Domains\Carrefour\CategoryCrawlerService;
use App\Domains\Carrefour\CategoryCrawlerServiceInterface;
use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Libraries\Context\AppContext;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tests\TestCase;

/**
 * @covers \App\Domains\Carrefour\CategoryCrawlerService
 */
class CategoryCrawlerServiceTest extends TestCase
{
    public function testCrawl_should_throw_exception_on_non_200_http_code(): void {
        try {
            $context = AppContext::background();

            $service = $this->getService(
                $this->genClient('', [
                    'http_code' => 500,
                ])
            );

            $crawlSpec = new CategoryCrawlInput();
            $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';

            $output = $service->crawl($context, $crawlSpec);

            $this->assertTrue($output->modified);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCrawl_should_as_modified_with_200_status(): void
    {
        $context = AppContext::background();

        $service = $this->getService(
            $this->genClient('', [
                'http_code' => 200,
            ])
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';

        $output = $service->crawl($context, $crawlSpec);

        $this->assertTrue($output->modified);
    }

    public function testCrawl_should_as_not_modified_with_304_status(): void
    {
        $context = AppContext::background();

        $service = $this->getService(
            $this->genClient('', [
                'http_code' => 204
            ])
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';

        $output = $service->crawl($context, $crawlSpec);

        $this->assertFalse($output->modified);
    }

    public function testCrawl_should_return_arbitrary_content(): void
    {
        $context = AppContext::background();

        $service = $this->getService(
            $this->genClient('foo', [
                'http_code' => 200,
            ])
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';

        $output = $service->crawl($context, $crawlSpec);

        $this->assertEquals('foo', $output->content);
    }

    public function testCrawl_should_return_response_headers(): void
    {
        $context = AppContext::background();

        $service = $this->getService(
            $this->genClient('', [
                'http_code' => 200,
                'response_headers' => [
                    'content-type' => 'text/html; charset=utf-8',
                    'content-length' => '0',
                    'Cookie' => 'foo=bar; bar=baz',
                ],
            ])
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';

        $output = $service->crawl($context, $crawlSpec);

        $this->assertIsArray($output->headers);
        $this->assertArrayHasKey('content-type', $output->headers);
        $this->assertEquals('text/html; charset=utf-8', $output->headers['content-type']);

        $this->assertArrayHasKey('content-length', $output->headers);
        $this->assertEquals('0', $output->headers['content-length']);

        $this->assertArrayHasKey('cookie', $output->headers);
        $this->assertEquals('foo=bar; bar=baz', $output->headers['cookie']);
    }

    public function testCrawl_should_call_http_client_with_correct_parameters(): void
    {
        $context = AppContext::background();

        $client = $this->genClient('', []);
        $service = $this->getService(
            $client
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';
        $crawlSpec->headers = [];
        $crawlSpec->timeout = 10;

        $service->crawl($context, $crawlSpec);

        $tracedRequests = $client->getTracedRequests();

        $this->assertIsArray($tracedRequests);
        $this->assertCount(1, $tracedRequests);

        $request = $tracedRequests[0];

        $this->assertArrayHasKey('method', $request);
        $this->assertEquals('GET', $request['method']);

        $this->assertArrayHasKey('url', $request);
        $this->assertEquals('https://www.carrefour.es/supermercado/bebidas/cat20003/c', $request['url']);
    }

    public function testCrawl_should_call_http_client_with_correct_headers(): void
    {
        $context = AppContext::background();

        $client = $this->genClient('', []);
        $service = $this->getService(
            $client
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';
        $crawlSpec->headers = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

        $service->crawl($context, $crawlSpec);

        $tracedRequests = $client->getTracedRequests();

        $this->assertIsArray($tracedRequests);
        $this->assertCount(1, $tracedRequests);

        $request = $tracedRequests[0];

        $this->assertArrayHasKey('options', $request);
        $this->assertIsArray($request['options']);
        $this->assertCount(2, $request['options']);
        $this->assertArrayHasKey('headers', $request['options']);

        $headers = $request['options']['headers'];

        $this->assertIsArray($headers);
        $this->assertCount(2, $headers);

        $this->assertArrayHasKey('foo', $headers);
        $this->assertEquals('bar', $headers['foo']);

        $this->assertArrayHasKey('bar', $headers);
        $this->assertEquals('baz', $headers['bar']);
    }

    public function testCrawl_should_call_http_client_with_correct_timeout(): void
    {
        $context = AppContext::background();

        $client = $this->genClient('', []);
        $service = $this->getService(
            $client
        );

        $crawlSpec = new CategoryCrawlInput();
        $crawlSpec->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';
        $crawlSpec->timeout = 4.5;

        $service->crawl($context, $crawlSpec);

        $tracedRequests = $client->getTracedRequests();

        $this->assertIsArray($tracedRequests);
        $this->assertCount(1, $tracedRequests);

        $request = $tracedRequests[0];

        $this->assertArrayHasKey('options', $request);
        $this->assertIsArray($request['options']);
        $this->assertCount(2, $request['options']);

        $this->assertArrayHasKey('timeout', $request['options']);
        $this->assertEquals(4.5, $request['options']['timeout']);
    }

    private function getService(HttpClientInterface $client): CategoryCrawlerServiceInterface
    {
        return new CategoryCrawlerService(
            $client,
            // $this->app->make(...) anything else
        );
    }

    /**
     * @param string $body
     * @param array<string, mixed> $options
     * @return TraceableHttpClient
     */
    private function genClient(string $body, array $options): TraceableHttpClient
    {
        return new TraceableHttpClient(
            new MockHttpClient(
                new MockResponse($body, $options)
            )
        );
    }
}
