<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Handler;

use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\ConversionProcessorInterface;
use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessConversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class ProcessConversionHandler
{
    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly ConversionProcessorInterface $conversionProcessor,
    ) {
    }

    public function __invoke(ProcessConversion $message): void
    {
        /** @var ConversionInterface|null $conversion */
        $conversion = $this->conversionRepository->find($message->conversion);
        if (!$conversion instanceof ConversionInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('A conversion with id %d does not exist', $message->conversion));
        }

        $this->conversionProcessor->process($conversion);
    }
}
