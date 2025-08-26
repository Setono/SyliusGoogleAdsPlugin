<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class DashboardAction
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/dashboard/index.html.twig'));
    }
}
