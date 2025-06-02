<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Psr\Log\LoggerInterface;
use Setono\SyliusGoogleAdsPlugin\Client\ClientInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @experimental
 */
interface ClientFactoryInterface
{
    public function createFromChannel(ChannelInterface $channel, LoggerInterface $logger = null): ClientInterface;
}
