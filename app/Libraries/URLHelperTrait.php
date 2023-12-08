<?php

namespace App\Libraries;

trait URLHelperTrait
{
    public function getBaseURIFromURL(string $url): string
    {
        $parsedURL = parse_url($url);
        if ($parsedURL && isset($parsedURL['scheme'], $parsedURL['host'])) {
            ['scheme' => $scheme,
                'host' => $host,] = $parsedURL;
            return $scheme . '://' . $host;
        }

        return '';
    }
}
