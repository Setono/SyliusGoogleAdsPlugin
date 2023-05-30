<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Command;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

final class ProcessConversion
{
    /**
     * The conversion id
     */
    public int $conversion;

    public function __construct(int|ConversionInterface $conversion)
    {
        if ($conversion instanceof ConversionInterface) {
            $conversion = (int) $conversion->getId();
        }

        $this->conversion = $conversion;
    }
}
