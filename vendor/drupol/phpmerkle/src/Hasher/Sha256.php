<?php

declare(strict_types=1);

namespace drupol\phpmerkle\Hasher;

/**
 * Class Sha256.
 */
final class Sha256 extends AbstractHasher
{
    /**
     * {@inheritdoc}
     */
    public function hash(string $data, bool $raw_output = true): string
    {
        return $this->doHash('sha256', $data, $raw_output);
    }

    /**
     * {@inheritdoc}
     */
    public function unpack(string $hash): string
    {
        return implode('', unpack('H*', $hash));
    }
}
