<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Form\Type\MapConversionActionIdType;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class SetupMapConversionActionIdAction extends AbstractSetupAction
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

        $form = $this->formFactory->create(MapConversionActionIdType::class, $connection);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager($connection)->flush();
        }

        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/connection/setup/map_conversion_action_id.html.twig', [
            'connection' => $connection,
            'form' => $form->createView(),
        ]));
    }
}
