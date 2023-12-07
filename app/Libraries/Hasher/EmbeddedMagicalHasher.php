<?php

namespace App\Libraries\Hasher;

class EmbeddedMagicalHasher implements HasherInterface
{
    /**
     * yep sha1 super magical, super lazy, for testing purposes only... maybe?
     *
     * @inheritDoc
     */
    public static function hash(HashableInterface|array|string $hashable): string
    {
        if (is_string($hashable)) {
            return hash('sha1', $hashable);
        } elseif ($hashable instanceof HashableInterface) {
            return hash('sha1', $hashable->hashable());
        }

        $hashes = [];
        foreach ($hashable as $item) {
            $hashes[] = self::hash($item);
        }

        usort($hashes, fn ($a, $b) => $a <=> $b);

        return hash('sha1', implode('', $hashes));
    }
}
