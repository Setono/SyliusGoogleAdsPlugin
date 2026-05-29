<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Protobuf\Internal\Message;

/**
 * @internal
 *
 * @experimental
 *
 * @template T of Message
 */
interface ReadableResourceInterface
{
    /**
     * @return T|null
     */
    public function get(string $resourceName): ?object;
}
