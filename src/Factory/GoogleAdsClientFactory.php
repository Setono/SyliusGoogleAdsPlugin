<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

final class GoogleAdsClientFactory implements GoogleAdsClientFactoryInterface
{
    public function create(
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $developerToken,
        int $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient {
        $tokenBuilder = (new OAuth2TokenBuilder())
            ->withClientId($clientId)
            ->withClientSecret($clientSecret)
            ->withRefreshToken($refreshToken);

        $builder = (new GoogleAdsClientBuilder())
            ->withDeveloperToken($developerToken)
            ->withOAuth2Credential($tokenBuilder->build())
            ->withLoginCustomerId($managerId)
        ;

        if (null !== $logger) {
            $builder->withLogger($logger)
                ->withLogLevel(LogLevel::DEBUG)
            ;
        }

        return $builder->build();
    }

    public function createFromConnection(
        ConnectionInterface $connection,
        int $managerId = null,
        LoggerInterface $logger = null,
    ): GoogleAdsClient {
        return $this->create(
            (string) $connection->getClientId(),
            (string) $connection->getClientSecret(),
            (string) $connection->getRefreshToken(),
            (string) $connection->getDeveloperToken(),
            $managerId,
            $logger,
        );
    }
}
