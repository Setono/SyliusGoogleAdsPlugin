<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Webmozart\Assert\Assert;

class Conversion implements ConversionInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $code = null;

    protected ?string $category = null;

    protected ?string $conversionId = null;

    protected ?string $conversionLabel = null;

    /** @var Collection|ChannelInterface[] */
    protected Collection $channels;

    public function __construct()
    {
        $this->channels = new ArrayCollection();
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ADD_TO_CART => self::CATEGORY_ADD_TO_CART,
            self::CATEGORY_BEGIN_CHECKOUT => self::CATEGORY_BEGIN_CHECKOUT,
            self::CATEGORY_BOOK_APPOINTMENT => self::CATEGORY_BOOK_APPOINTMENT,
            self::CATEGORY_CONTACT => self::CATEGORY_CONTACT,
            self::CATEGORY_GET_DIRECTIONS => self::CATEGORY_GET_DIRECTIONS,
            self::CATEGORY_OTHER => self::CATEGORY_OTHER,
            self::CATEGORY_OUTBOUND_CLICK => self::CATEGORY_OUTBOUND_CLICK,
            self::CATEGORY_PAGE_VIEW => self::CATEGORY_PAGE_VIEW,
            self::CATEGORY_PURCHASE => self::CATEGORY_PURCHASE,
            self::CATEGORY_REQUEST_QUOTE => self::CATEGORY_REQUEST_QUOTE,
            self::CATEGORY_SIGN_UP => self::CATEGORY_SIGN_UP,
            self::CATEGORY_SUBMIT_LEAD_FORM => self::CATEGORY_SUBMIT_LEAD_FORM,
            self::CATEGORY_SUBSCRIBE => self::CATEGORY_SUBSCRIBE,
        ];
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        Assert::oneOf($category, static::getCategories());

        $this->category = $category;
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
