<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Builder;

use Google\Ads\GoogleAds\Lib\Configuration;
use Google\Ads\GoogleAds\Lib\GoogleAdsBuilder;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder as BaseGoogleAdsClientBuilder;
use Google\Auth\FetchAuthTokenInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

final class GoogleAdsClientBuilder implements GoogleAdsBuilder
{
    public function __construct(private readonly BaseGoogleAdsClientBuilder $decorated)
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

    public function build()
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

    public function fromConnectionAndOAuthCredentials(ConnectionInterface $connection, FetchAuthTokenInterface $oauthCredentials): self
    {
        $this->decorated->withDeveloperToken($connection->getDeveloperToken());
        $this->decorated->withOAuth2Credential($oauthCredentials);

        return $this;
    }

    public function withLoginCustomerId(?int $loginCustomerId)
    {
        return $this->decorated->withLoginCustomerId($loginCustomerId);
    }
}
