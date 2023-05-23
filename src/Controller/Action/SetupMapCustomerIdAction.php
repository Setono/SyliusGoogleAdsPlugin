<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Form\Type\MapCustomerIdType;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class SetupMapCustomerIdAction extends AbstractSetupAction
{
    use ORMManagerTrait;

    public function __construct(
        Environment $twig,
        ConnectionRepositoryInterface $connectionRepository,
        UrlGeneratorInterface $urlGenerator,
        private readonly FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
    ) {
        parent::__construct($twig, $connectionRepository, $urlGenerator);

        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request, int $connectionId): Response
    {
        $connection = $this->getConnection($connectionId);

        $form = $this->formFactory->create(MapCustomerIdType::class, $connection);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager($connection)->flush();

            return new RedirectResponse($this->urlGenerator->generate('setono_sylius_google_ads_admin_setup_map_conversion_action_id', [
                'connectionId' => $connectionId,
            ]));
        }

        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/connection/setup/map_customer_id.html.twig', [
            'connection' => $connection,
            'form' => $form->createView(),
        ]));
    }
}
