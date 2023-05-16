<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Setono\SyliusGoogleAdsPlugin\Builder\GoogleAdsClientBuilder;
use Setono\SyliusGoogleAdsPlugin\Builder\OAuth2TokenBuilder;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

final class ConversionActionIdsResolver implements ConversionActionIdsResolverInterface
{
    public function __construct(
        private readonly OAuth2TokenBuilder $oauth2TokenBuilder,
        private readonly GoogleAdsClientBuilder $googleAdsClientBuilder,
    ) {
    }

    public function getConversionActionIdsFromConnectionAndCustomer(
        ConnectionInterface $connection,
        int $customerId,
    ): array {
        $oauthCredentials = $this->oauth2TokenBuilder->fromConnection($connection)->build();
        $client = $this->googleAdsClientBuilder
            ->fromConnectionAndOAuthCredentials($connection, $oauthCredentials)
            ->withLoginCustomerId($customerId)
            ->build()
        ;

    }
}
