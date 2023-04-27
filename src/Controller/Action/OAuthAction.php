<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// See example here: https://github.com/googleads/google-ads-php/blob/v19.0.0/examples/Authentication/GenerateUserCredentials.php
// todo this class feels clumsy
final class OAuthAction
{
    use ORMManagerTrait;

    private const SESSION_NAME = 'ssga_connection_id';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RepositoryInterface $connectionRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        ManagerRegistry $managerRegistry,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request, int $connectionId = null): Response
    {
        // if this is true we are sending an oauth request to Google
        $oauthRequest = true;

        if (null === $connectionId) {
            $oauthRequest = false;
            /** @var mixed $connectionId */
            $connectionId = $this->requestStack->getSession()->get(self::SESSION_NAME);
            if (!is_int($connectionId)) {
                throw new \RuntimeException('A connection id could not be resolved'); // todo handle with redirect and flash message
            }
        }

        $this->requestStack->getSession()->set(self::SESSION_NAME, $connectionId);

        /** @var ConnectionInterface|null $connection */
        $connection = $this->connectionRepository->find($connectionId);
        if (null === $connection) {
            throw new \InvalidArgumentException(sprintf('A connection with id %d does not exist', $connectionId));
        }

        $oauth2 = new OAuth2([
            'clientId' => $connection->getClientId(),
            'clientSecret' => $connection->getClientSecret(),
            'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'redirectUri' => $this->urlGenerator->generate('setono_sylius_google_ads_admin_oauth', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'scope' => 'https://www.googleapis.com/auth/adwords',
            // Create a 'state' token to prevent request forgery. See
            // https://developers.google.com/identity/protocols/OpenIDConnect#createxsrftoken
            // for details.
            'state' => bin2hex(random_bytes(16)), // todo check the state token
        ]);

        if ($oauthRequest) {
            return new RedirectResponse((string) $oauth2->buildFullAuthorizationUri([
                'access_type' => 'offline',
            ]));
        }

        $code = $request->query->get('code');
        if (!is_string($code)) {
            throw new \InvalidArgumentException('The oauth response did not contain a code');
        }

        $oauth2->setCode($code);
        $credentials = $oauth2->fetchAuthToken();
        if (!isset($credentials['access_token']) || !is_string($credentials['access_token'])) {
            throw new \InvalidArgumentException('The access token is not valid');
        }

        $connection->setAccessToken($credentials['access_token']);

        $this->getManager($connection)->flush();

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_update', [
            'id' => $connection->getId(),
        ]));
    }
}
