<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests;

use Setono\SyliusGoogleAdsPlugin\Model\Connection;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMapping;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Webmozart\Assert\Assert;

trait LiveTestTrait
{
    public static function isLive(): bool
    {
        $live = getenv('GOOGLE_ADS_LIVE');

        return is_string($live) && true === (bool) $live;
    }

    public static function getManagerId(): string
    {
        $managerId = getenv('GOOGLE_ADS_MANAGER_ID');
        Assert::stringNotEmpty($managerId);

        return $managerId;
    }

    public static function getCustomerId(): string
    {
        $customerId = getenv('GOOGLE_ADS_CUSTOMER_ID');
        Assert::stringNotEmpty($customerId);

        return $customerId;
    }

    public static function createConnection(): ConnectionInterface
    {
        $clientId = getenv('GOOGLE_ADS_CLIENT_ID');
        Assert::stringNotEmpty($clientId);

        $clientSecret = getenv('GOOGLE_ADS_CLIENT_SECRET');
        Assert::stringNotEmpty($clientSecret);

        $refreshToken = getenv('GOOGLE_ADS_REFRESH_TOKEN');
        Assert::stringNotEmpty($refreshToken);

        $developerToken = getenv('GOOGLE_ADS_DEVELOPER_TOKEN');
        Assert::stringNotEmpty($developerToken);

        $connection = new Connection();
        $connection->setClientId($clientId);
        $connection->setClientSecret($clientSecret);
        $connection->setRefreshToken($refreshToken);
        $connection->setDeveloperToken($developerToken);

        return $connection;
    }

    public static function createConnectionMapping(): ConnectionMappingInterface
    {
        $mapping = new ConnectionMapping();
        $mapping->setManagerId(self::getManagerId());
        $mapping->setCustomerId(self::getCustomerId());

        $connection = self::createConnection();
        $connection->addConnectionMapping($mapping);

        return $mapping;
    }
}
