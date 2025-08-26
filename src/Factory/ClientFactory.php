<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Psr\Log\LoggerInterface;
use Setono\SyliusGoogleAdsPlugin\Client\Client;
use Setono\SyliusGoogleAdsPlugin\Client\ClientInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class ClientFactory implements ClientFactoryInterface
{
    public function __construct(
        private readonly ConnectionMappingRepositoryInterface $connectionMappingRepository,
        private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
    ) {
    }

    public function createFromChannel(ChannelInterface $channel, LoggerInterface $logger = null): ClientInterface
    {
        $connectionMapping = $this->connectionMappingRepository->findOneEnabledByChannel($channel);
        Assert::notNull($connectionMapping, sprintf('No connection mapping found for channel %s', (string) $channel->getName()));

        $connection = $connectionMapping->getConnection();
        Assert::notNull($connection);

        $managerId = $connectionMapping->getManagerId();
        Assert::notNull($managerId);

        $customerId = $connectionMapping->getCustomerId();
        Assert::notNull($customerId);

        return new Client($this->googleAdsClientFactory->createFromConnection($connection, $managerId, $logger), $customerId);
    }
}
