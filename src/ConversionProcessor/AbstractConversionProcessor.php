<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractConversionProcessor implements ConversionProcessorInterface
{
    public function __construct(
        protected readonly WorkflowInterface $workflow,
        protected readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
        protected readonly ConnectionMappingRepositoryInterface $connectionMappingRepository,
    ) {
    }
}
