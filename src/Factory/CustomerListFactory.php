<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class CustomerListFactory implements CustomerListFactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $decorated,
        private readonly int $defaultMembershipLifespan,
    ) {
    }

    public function createNew(): CustomerListInterface
    {
        $obj = $this->decorated->createNew();
        Assert::isInstanceOf($obj, CustomerListInterface::class);

        $obj->setMembershipLifespan($this->defaultMembershipLifespan);

        return $obj;
    }
}
