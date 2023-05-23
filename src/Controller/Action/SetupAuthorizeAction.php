<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetupAuthorizeAction extends AbstractSetupAction
{
    public function __invoke(Request $request, int $connectionId): Response
    {
        $connection = $this->getConnection($connectionId);

        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/connection/setup/authorize.html.twig', [
            'connection' => $connection,
        ]));
    }
}
