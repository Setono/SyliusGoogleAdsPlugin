<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClient;
use Psr\Log\LoggerInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

interface GoogleAdsClientFactoryInterface
{
    public function create(
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $developerToken,
        int $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient;

    public function createFromConnection(
        ConnectionInterface $connection,
        int $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient;
}
