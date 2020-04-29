<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Conversion implements ConversionInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $code = null;

    protected ?string $conversionId = null;

    protected ?string $conversionLabel = null;

    /** @var Collection|ChannelInterface[] */
    protected Collection $channels;

    public function __construct()
    {
        $this->channels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getConversionId(): ?string
    {
        return $this->conversionId;
    }

    public function setConversionId(string $conversionId): void
    {
        $this->conversionId = $conversionId;
    }

    public function getConversionLabel(): ?string
    {
        return $this->conversionLabel;
    }

    public function setConversionLabel(string $conversionLabel): void
    {
        $this->conversionLabel = $conversionLabel;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }
}
