<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Form\Type\MapConnectionType;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class SetupMapAction extends AbstractAuthorizeAction
{
    use ORMManagerTrait;

    public function __construct(
        Environment $twig,
        ConnectionRepositoryInterface $connectionRepository,
        private readonly FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
    ) {
        parent::__construct($twig, $connectionRepository);

        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request, int $connectionId): Response
    {
        $connection = $this->getConnection($connectionId);

        $form = $this->formFactory->create(MapConnectionType::class, $connection);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager($connection)->flush();
        }

        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/connection/setup/map.html.twig', [
            'connection' => $connection,
            'form' => $form->createView(),
        ]));
    }
}
