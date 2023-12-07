<?php

namespace App\Libraries\Hasher;

interface HasherInterface
{
    /**
     * @param HashableInterface|list<HashableInterface>|string $hashable
     * @return string
     */
    public static function hash(HashableInterface|array|string $hashable): string;
}
