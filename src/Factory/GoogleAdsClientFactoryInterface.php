<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Google\Ads\GoogleAds\Lib\V15\GoogleAdsClient;
use Psr\Log\LoggerInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

/**
 * Marked as internal because the return type changes when upgrading the Google Ads PHP SDK
 *
 * @internal
 */
interface GoogleAdsClientFactoryInterface
{
    public function create(
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $developerToken,
        string $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient;

    public function createFromConnection(
        ConnectionInterface $connection,
        string $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient;
}
