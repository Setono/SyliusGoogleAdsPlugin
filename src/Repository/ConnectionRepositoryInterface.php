<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConnectionInterface>
 */
interface ConnectionRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns true if there are 1 or more connections in the database
     */
    public function hasAny(): bool;
}
