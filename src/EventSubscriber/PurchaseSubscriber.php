<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Event\PrePersistConversionFromOrderEvent;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    use ORMManagerTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ConversionFactoryInterface $conversionFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack $requestStack,
        private readonly string $cookieName,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.pre_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        /** @var mixed|OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return;
        }

        $gclid = $request->cookies->get($this->cookieName);
        if (!is_string($gclid) || '' === $gclid) {
            return;
        }

        $conversion = $this->conversionFactory->createFromOrder($order);
        $conversion->setGoogleClickId($gclid);

        $userAgent = $request->headers->get('User-Agent');
        if (is_string($userAgent) && '' !== $userAgent) {
            $conversion->setUserAgent($userAgent);
        }

        $this->eventDispatcher->dispatch(new PrePersistConversionFromOrderEvent($conversion, $order));

        $this->getManager($conversion)->persist($conversion);
    }
}
