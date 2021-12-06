<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusGoogleAdsPlugin\ConsentChecker\ConsentCheckerInterface;
use Setono\SyliusGoogleAdsPlugin\Event\PrePersistConversionFromOrderEvent;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionActionRepositoryInterface;
use function sprintf;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    /** @var ObjectManager[] */
    private array $managers = [];

    private ConversionActionRepositoryInterface $conversionActionRepository;

    private ConversionFactoryInterface $conversionFactory;

    private ManagerRegistry $managerRegistry;

    private ConsentCheckerInterface $consentChecker;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ConversionActionRepositoryInterface $conversionActionRepository,
        ConversionFactoryInterface $conversionFactory,
        ManagerRegistry $managerRegistry,
        ConsentCheckerInterface $consentChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->conversionActionRepository = $conversionActionRepository;
        $this->conversionFactory = $conversionFactory;
        $this->managerRegistry = $managerRegistry;
        $this->consentChecker = $consentChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface|mixed $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class, sprintf(
            'You must implement the %s in your Sylius application. Read the readme here: https://github.com/Setono/SyliusGoogleAdsPlugin',
            OrderInterface::class
        ));

        $channel = $order->getChannel();
        if (null === $channel) {
            return;
        }

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

    private function getManager(object $obj): ObjectManager
    {
        $cls = get_class($obj);
        if (!isset($this->managers[$cls])) {
            $manager = $this->managerRegistry->getManagerForClass($cls);
            if (null === $manager) {
                throw new \InvalidArgumentException(sprintf('No object manager registered for class %s', $cls));
            }

            $this->managers[$cls] = $manager;
        }

        return $this->managers[$cls];
    }
}
