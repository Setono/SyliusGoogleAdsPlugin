<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait OrderTrait
{
    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $googleClickId = null;

    public function getGoogleClickId(): ?string
    {
        return $this->googleClickId;
    }

    public function setGoogleClickId(?string $googleClickId): void
    {
        $this->googleClickId = $googleClickId;
    }
}
