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
 * @extends CreatableResourceInterface<T>
 */
interface UpdatableResourceInterface extends CreatableResourceInterface
{
    /**
     * @param T $obj
     *
     * @return string The Google Ads resource name
     */
    public function update(object $obj): string;

    /**
     * If the resource is created, the $create callback will be called first, then the $update callback
     * If the resource is updated, only the $update callback is called
     *
     * @param callable(T): void $create
     * @param callable(T): void $update
     *
     * @return string The Google Ads resource name
     */
    public function createOrUpdate(?string $resourceName, callable $create, callable $update): string;
}
