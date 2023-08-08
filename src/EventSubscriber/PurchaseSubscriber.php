<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Event\PrePersistConversionFromOrderEvent;
use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    use ORMManagerTrait;

    public function __construct(
        private readonly ConversionFactoryInterface $conversionFactory,
        ManagerRegistry $managerRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        /** @var mixed|OrderInterface $order */
        $order = $event->getSubject();
        WrongOrderTypeException::assert($order);

        if ($order->getGoogleClickId() === null) {
            return;
        }

        $channel = $order->getChannel();
        Assert::notNull($channel);

        $conversion = $this->conversionFactory->createFromOrder($order);
        $conversion->setChannel($channel);

        $this->eventDispatcher->dispatch(new PrePersistConversionFromOrderEvent($conversion, $order));

        $manager = $this->getManager($conversion);
        $manager->persist($conversion);

        $manager->flush();
    }
}
