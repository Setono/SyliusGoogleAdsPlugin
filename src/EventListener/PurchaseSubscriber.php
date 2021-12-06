<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\ConsentChecker\ConsentCheckerInterface;
use Setono\SyliusGoogleAdsPlugin\Event\PrePersistConversionFromOrderEvent;
use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionActionRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    use ORMManagerTrait;

    private ConversionActionRepositoryInterface $conversionActionRepository;

    private ConversionFactoryInterface $conversionFactory;

    private ConsentCheckerInterface $consentChecker;

    private EventDispatcherInterface $eventDispatcher;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        ConversionActionRepositoryInterface $conversionActionRepository,
        ConversionFactoryInterface $conversionFactory,
        ManagerRegistry $managerRegistry,
        ConsentCheckerInterface $consentChecker,
        EventDispatcherInterface $eventDispatcher,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->conversionActionRepository = $conversionActionRepository;
        $this->conversionFactory = $conversionFactory;
        $this->managerRegistry = $managerRegistry;
        $this->consentChecker = $consentChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        if (!$requestEvent->isMasterRequest()) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if ('sylius_shop_order_thank_you' !== $route) {
            return;
        }

        /** @var mixed $orderId */
        $orderId = $request->getSession()->get('sylius_order_id');

        if (!is_scalar($orderId)) {
            return;
        }

        $order = $this->orderRepository->find($orderId);
        WrongOrderTypeException::assert($order);

        $channel = $order->getChannel();
        Assert::notNull($channel);

        if (!$this->consentChecker->hasConsent()) {
            return;
        }

        $conversionActions = $this->conversionActionRepository->findEnabledByChannelAndCategory(
            $channel,
            ConversionActionInterface::CATEGORY_PURCHASE
        );

        $manager = null;

        foreach ($conversionActions as $conversionAction) {
            $conversion = $this->conversionFactory->createFromOrder($order, (string) $conversionAction->getCategory());
            $conversion->setName((string) $conversionAction->getName());
            $conversion->setChannel($channel);

            $this->eventDispatcher->dispatch(
                new PrePersistConversionFromOrderEvent($conversion, $conversionAction, $order)
            );

            $manager = $this->getManager($conversion);
            $manager->persist($conversion);
        }

        if (null !== $manager) {
            $manager->flush();
        }
    }
}
