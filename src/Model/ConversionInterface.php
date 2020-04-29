<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface ConversionInterface extends ResourceInterface, CodeAwareInterface, ToggleableInterface, ChannelsAwareInterface
{
    public function getId(): ?int;

    public function getConversionId(): ?string;

    public function setConversionId(string $conversionId): void;

    public function getConversionLabel(): ?string;

    public function setConversionLabel(string $conversionLabel): void;
}
