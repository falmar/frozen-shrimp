<?php


namespace App\Libraries\Context;


use Psr\Http\Message\ServerRequestInterface;

class AppContext implements Context
{
    private const REQUEST_CONTEXT_KEY = 'slim-skeleton/main-context';

    /** @var array<string|int, mixed> */
    protected array $attributes = [];

    /**
     * @param array<string|int, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $k => $v) {
            $this->attributes[$k] = $v;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return ($this->attributes[$key] ??= $default);
    }

    /**
     * @inheritDoc
     */
    public function withAttribute(string $key, mixed $value): Context
    {
        $this->attributes[$key] = $value;

        return new self($this->getAttributes());
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute(string $key): Context
    {
        unset($this->attributes[$key]);

        return new self($this->getAttributes());
    }

    public static function withRequest(ServerRequestInterface $request, Context $context): ServerRequestInterface
    {
        return $request->withAttribute(
            AppContext::REQUEST_CONTEXT_KEY,
            $context
        );
    }

    public static function fromRequest(ServerRequestInterface $request): ?Context
    {
        /** @var Context|null $ctx */
        $ctx = $request->getAttribute(
            AppContext::REQUEST_CONTEXT_KEY,
            null
        );

        return $ctx;
    }

    /**
     * Create base context
     *
     * @param array<string|int, mixed> $attributes
     * @return Context
     */
    public static function background(array $attributes = []): Context
    {
        return new AppContext($attributes);
    }
}
