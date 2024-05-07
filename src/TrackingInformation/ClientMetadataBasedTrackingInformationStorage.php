<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Setono\ClientBundle\Context\ClientContextInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Webmozart\Assert\Assert;

final class ClientMetadataBasedTrackingInformationStorage extends AbstractTrackingInformationStorage
{
    public function __construct(
        private readonly ClientContextInterface $clientContext,
        private readonly string $metadataKey = 'ssga_tracking_information',
    ) {
        parent::__construct();
    }

    public function get(): ?TrackingInformation
    {
        $clientMetadata = $this->clientContext->getClient()->metadata;
        if (!$clientMetadata->has($this->metadataKey)) {
            return null;
        }

        try {
            $data = $clientMetadata->get($this->metadataKey);
            Assert::isArray($data);

            return TrackingInformation::fromArray($data);
        } catch (\InvalidArgumentException) {
            // the data is corrupted, remove it
            $clientMetadata->remove($this->metadataKey);

            return null;
        }
    }

    public function persist(ResponseEvent $event): void
    {
        if (null === $this->trackingInformation) {
            return;
        }

        $this->clientContext->getClient()->metadata->set($this->metadataKey, $this->trackingInformation);
    }
}
