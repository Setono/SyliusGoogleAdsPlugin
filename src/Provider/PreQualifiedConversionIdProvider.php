<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Provider;

use DoctrineBatchUtils\BatchProcessing\SelectBatchIteratorAggregate;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;

final class PreQualifiedConversionIdProvider implements ConversionIdProviderInterface
{
    public function __construct(private readonly ConversionRepositoryInterface $conversionRepository)
    {
    }

    public function getConversionIds(): iterable
    {
        $iterator = SelectBatchIteratorAggregate::fromQuery(
            $this->conversionRepository->createPreQualifiedConversionQueryBuilder()->select('o.id')->getQuery(),
            100,
        );

        /** @var array{id: int} $item */
        foreach ($iterator as $item) {
            yield $item['id'];
        }
    }
}
