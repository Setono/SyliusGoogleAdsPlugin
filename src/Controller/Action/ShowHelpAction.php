<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Setono\SyliusGoogleAdsPlugin\Repository\ConversionActionRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ShowHelpAction
{
    private Environment $twig;

    private ConversionActionRepositoryInterface $conversionRepository;

    public function __construct(
        Environment $twig,
        ConversionActionRepositoryInterface $conversionRepository
    ) {
        $this->twig = $twig;
        $this->conversionRepository = $conversionRepository;
    }

    public function __invoke(Request $request): Response
    {
        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/admin/help.html.twig', [
            'channels' => $this->conversionRepository->findChannels(),
        ]));
    }
}
