<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use InvalidArgumentException;

final class AiManager
{
    /**
     * @param  array<string, AiProviderInterface>  $providers
     */
    public function __construct(
        private readonly array $providers,
        private readonly string $defaultProvider = 'local'
    ) {}

    public function provider(?string $name = null): AiProviderInterface
    {
        $key = $name ?? $this->defaultProvider;
        if (! array_key_exists($key, $this->providers)) {
            throw new InvalidArgumentException(sprintf('AI provider [%s] is not configured.', $key));
        }

        return $this->providers[$key];
    }
}
