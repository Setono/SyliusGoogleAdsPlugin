<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

abstract class AbstractSetupAction
{
    public function __construct(
        protected readonly Environment $twig,
        protected readonly ConnectionRepositoryInterface $connectionRepository,
        protected readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    protected function getConnection(int $connectionId): ConnectionInterface
    {
        $connection = $this->connectionRepository->find($connectionId);
        if (!$connection instanceof ConnectionInterface) {
            throw new NotFoundHttpException(sprintf('The connection with id %d does not exist', $connectionId));
        }

        return $connection;
    }
}
