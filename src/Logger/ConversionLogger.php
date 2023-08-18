<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Logger;

use Psr\Log\AbstractLogger;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

final class ConversionLogger extends AbstractLogger
{
    public function __construct(private readonly ConversionInterface $conversion)
    {
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->conversion->addLogMessage((string) $message);
    }
}
