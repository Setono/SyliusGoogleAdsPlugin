<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Event\PrePersistConversionFromOrderEvent;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformationStorageInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class PurchaseListener implements LoggerAwareInterface
{
    use ORMManagerTrait;

    private LoggerInterface $logger;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ConversionFactoryInterface $conversionFactory,
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack $requestStack,
        private readonly TrackingInformationStorageInterface $trackingInformationStorage,
        private readonly bool $flush = false,
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->logger = new NullLogger();
    }

    public function track(ResourceControllerEvent $event): void
    {
        try {
            /** @var mixed|OrderInterface $order */
            $order = $event->getSubject();
            Assert::isInstanceOf($order, OrderInterface::class);

            if (null !== $this->conversionRepository->findOneByOrder($order)) {
                return;
            }

            $request = $this->requestStack->getMainRequest();
            Assert::notNull($request);

            $conversion = $this->conversionFactory->createFromOrder($order);

            $trackingInformation = $this->trackingInformationStorage->get();
            if (null === $trackingInformation) {
                return;
            }

            $trackingInformation->assignToConversion($conversion);

            $userAgent = $request->headers->get('User-Agent');
            Assert::stringNotEmpty($userAgent);

            $conversion->setUserAgent($userAgent);

            $this->eventDispatcher->dispatch(new PrePersistConversionFromOrderEvent($conversion, $order));

            $manager = $this->getManager($conversion);
            $manager->persist($conversion);
            if ($this->flush) {
                $manager->flush();
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'An error occurred when trying to track a Google Ads conversion: %s',
                $e->getMessage(),
            ));
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
