<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Protobuf\Internal\Message;

/**
 * @internal
 *
 * @template T of Message
 */
interface CreatableResourceInterface
{
    /**
     * @param T $obj
     *
     * @return string The Google Ads resource name
     */
    public function create(object $obj): string;
}
