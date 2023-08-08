<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RedirectMainMenuAction
{
    public function __construct(
        private readonly ConnectionRepositoryInterface $connectionRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        if ($this->connectionRepository->hasAny()) {
            return new RedirectResponse($this->urlGenerator->generate('setono_sylius_google_ads_admin_conversion_index'));
        }

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_index'));
    }
}
