<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NormalizerInterface;
use App\Exceptions\DocumentNormalizationException;

final class DocumentNormalizerService
{
    /**
     * @param  iterable<NormalizerInterface>  $normalizers
     */
    public function __construct(private readonly iterable $normalizers) {}

    /**
     * @return array{content:string,extension:string}
     */
    public function normalize(string $content, string $extension): array
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supports($extension)) {
                return $normalizer->normalize($content, $extension);
            }
        }

        throw new DocumentNormalizationException(
            sprintf('No normalizer found for extension: %s', $extension),
            true
        );
    }
}
