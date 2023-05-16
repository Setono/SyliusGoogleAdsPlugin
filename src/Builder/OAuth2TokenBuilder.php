<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Builder;

use Google\Ads\GoogleAds\Lib\Configuration;
use Google\Ads\GoogleAds\Lib\GoogleAdsBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder as BaseOAuth2TokenBuilder;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Credentials\UserRefreshCredentials;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

final class OAuth2TokenBuilder implements GoogleAdsBuilder
{
    public function __construct(private readonly BaseOAuth2TokenBuilder $decorated)
    {
    }

    public function from(Configuration $configuration)
    {
        return $this->decorated->from($configuration);
    }

    public function fromEnvironmentVariablesConfiguration(Configuration $configuration)
    {
        return $this->decorated->fromEnvironmentVariablesConfiguration($configuration);
    }

    public function fromFile(string $path)
    {
        return $this->decorated->fromFile($path);
    }

    public function fromEnvironmentVariables()
    {
        return $this->decorated->fromEnvironmentVariables();
    }

    public function build(): ServiceAccountCredentials|UserRefreshCredentials
    {
        return $this->decorated->build();
    }

    public function defaultOptionals(): void
    {
        $this->decorated->defaultOptionals();
    }

    public function validate(): void
    {
        $this->decorated->validate();
    }

    public function fromConnection(ConnectionInterface $connection): self
    {
        $this->decorated->withClientId($connection->getClientId());
        $this->decorated->withClientSecret($connection->getClientSecret());
        $this->decorated->withRefreshToken($connection->getAccessToken());

        return $this;
    }
}
