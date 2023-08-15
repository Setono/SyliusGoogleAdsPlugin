<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessConversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class RetryConversionAction
{
    use ORMManagerTrait;

    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly WorkflowInterface $conversionWorkflow,
        ManagerRegistry $managerRegistry,
        private readonly MessageBusInterface $commandBus,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request, int $id): Response
    {
        /** @var ConversionInterface|null $conversion */
        $conversion = $this->conversionRepository->find($id);

        if (null === $conversion) {
            return $this->createRedirect($request, 'error', 'setono_sylius_google_ads.conversion_does_not_exist');
        }

        if (!$this->conversionWorkflow->can($conversion, ConversionWorkflow::TRANSITION_RETRY)) {
            return $this->createRedirect($request, 'error', 'setono_sylius_google_ads.conversion_retrying_failed');
        }

        $this->conversionWorkflow->apply($conversion, ConversionWorkflow::TRANSITION_RETRY);

        $this->getManager($conversion)->flush();

        $this->commandBus->dispatch(new ProcessConversion($conversion));

        return $this->createRedirect($request, 'success', 'setono_sylius_google_ads.conversion_retrying_succeeded');
    }

    private function createRedirect(Request $request, string $messageType, string $message): RedirectResponse
    {
        $session = $request->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add($messageType, $message);
        }

        $redirect = $request->headers->get('referer');
        if (!is_string($redirect) || '' === $redirect) {
            $redirect = $this->urlGenerator->generate('setono_sylius_google_ads_admin_conversion_index');
        }

        return new RedirectResponse($redirect);
    }
}
