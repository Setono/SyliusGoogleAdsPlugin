<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class OAuthResponseAction extends AbstractOAuthAction
{
    use ORMManagerTrait;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        private readonly RepositoryInterface $connectionRepository,
        ManagerRegistry $managerRegistry,
    ) {
        parent::__construct($urlGenerator);

        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request): Response
    {
        $session = $request->getSession();

        try {
            /** @var mixed $connectionId */
            $connectionId = $session->get(self::SESSION_CONNECTION_ID);
            Assert::integer($connectionId);

            $expectedState = $session->get(self::SESSION_STATE_NAME);
            Assert::stringNotEmpty($expectedState);

            $actualState = $request->query->get('state');
            Assert::same($expectedState, $actualState);

            $code = $request->query->get('code');
            Assert::stringNotEmpty($code, 'The OAuth response did not contain a code');
        } catch (\InvalidArgumentException $e) {
            return $this->addFlashAndRedirect(
                $request,
                $e->getMessage(),
                $this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_index'),
            );
        }

        /** @var ConnectionInterface|null $connection */
        $connection = $this->connectionRepository->find($connectionId);
        if (null === $connection) {
            throw new NotFoundHttpException(sprintf('A connection with id %d does not exist', $connectionId));
        }

        $oauth2 = $this->buildOAuth($connection, $expectedState);
        $oauth2->setCode($code);
        $credentials = $oauth2->fetchAuthToken();
        if (!isset($credentials['refresh_token']) || !is_string($credentials['refresh_token'])) {
            throw new \InvalidArgumentException('The access token is not valid');
        }

        $connection->setRefreshToken($credentials['refresh_token']);

        $this->getManager($connection)->flush();

        return $this->addFlashAndRedirect(
            $request,
            'setono_sylius_google_ads.refresh_token_updated',
            $this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_update', ['id' => $connection->getId()]),
            'success',
        );
    }
}
