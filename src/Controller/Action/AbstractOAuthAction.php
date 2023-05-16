<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractOAuthAction
{
    protected const SESSION_STATE_NAME = 'ssga_state';

    protected const SESSION_CONNECTION_ID = 'ssga_connection_id';

    public function __construct(protected readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    protected function buildOAuth(ConnectionInterface $connection, string $state): OAuth2
    {
        return new OAuth2([
            'clientId' => $connection->getClientId(),
            'clientSecret' => $connection->getClientSecret(),
            'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'redirectUri' => $this->urlGenerator->generate(
                'setono_sylius_google_ads_admin_oauth_response',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
            'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'scope' => 'https://www.googleapis.com/auth/adwords',
            'state' => $state, // read about the state parameter here: https://developers.google.com/identity/protocols/OpenIDConnect#createxsrftoken
        ]);
    }

    protected function addFlashAndRedirect(Request $request, string $flash, string $url, string $type = 'error'): RedirectResponse
    {
        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add($type, $flash);
        }

        return new RedirectResponse($url);
    }
}
