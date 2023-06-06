<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait OrderTrait
{
    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $googleClickId = null;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $userAgent = null;

    public function getGoogleClickId(): ?string
    {
        return $this->googleClickId;
    }

    public function setGoogleClickId(?string $googleClickId): void
    {
        $this->googleClickId = $googleClickId;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }
}
