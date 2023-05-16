<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This OAuth request is based on the example from the Google Ads PHP library:
 * https://github.com/googleads/google-ads-php/blob/v19.0.0/examples/Authentication/GenerateUserCredentials.php
 */
final class OAuthRequestAction extends AbstractOAuthAction
{
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        private readonly RepositoryInterface $connectionRepository,
    ) {
        parent::__construct($urlGenerator);
    }

    public function __invoke(Request $request, int $connectionId): Response
    {
        /** @var ConnectionInterface|null $connection */
        $connection = $this->connectionRepository->find($connectionId);
        if (null === $connection) {
            throw new NotFoundHttpException(sprintf('A connection with id %d does not exist', $connectionId));
        }

        try {
            $state = bin2hex(random_bytes(16));
            $request->getSession()->set(self::SESSION_STATE_NAME, $state);
            $request->getSession()->set(self::SESSION_CONNECTION_ID, $connectionId);

            $oauth2 = $this->buildOAuth($connection, $state);

            return new RedirectResponse((string) $oauth2->buildFullAuthorizationUri([
                'access_type' => 'offline',
            ]));
        } catch (\Throwable $e) {
            return $this->addFlashAndRedirect(
                $request,
                $e->getMessage(),
                $this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_update', ['id' => $connectionId]),
            );
        }
    }
}
