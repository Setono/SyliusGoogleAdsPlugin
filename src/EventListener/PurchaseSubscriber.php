<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use function Safe\sprintf;
use Setono\SyliusGoogleAdsPlugin\ConsentChecker\ConsentCheckerInterface;
use Setono\SyliusGoogleAdsPlugin\Factory\ConversionFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionActionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    /** @var ObjectManager[] */
    private array $managers = [];

    private RequestStack $requestStack;

    private ConversionActionRepositoryInterface $conversionActionRepository;

    private ConversionFactoryInterface $conversionFactory;

    private ManagerRegistry $managerRegistry;

    private ConsentCheckerInterface $consentChecker;

    private string $cookieName;

    public function __construct(
        RequestStack $requestStack,
        ConversionActionRepositoryInterface $conversionActionRepository,
        ConversionFactoryInterface $conversionFactory,
        ManagerRegistry $managerRegistry,
        ConsentCheckerInterface $consentChecker,
        string $cookieName
    ) {
        $this->requestStack = $requestStack;
        $this->conversionActionRepository = $conversionActionRepository;
        $this->conversionFactory = $conversionFactory;
        $this->managerRegistry = $managerRegistry;
        $this->consentChecker = $consentChecker;
        $this->cookieName = $cookieName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $channel = $order->getChannel();
        if (null === $channel) {
            return;
        }

        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        if (!$request->cookies->has($this->cookieName)) {
            return;
        }

        if (!$this->consentChecker->hasConsent()) {
            return;
        }

        $conversionActions = $this->conversionActionRepository->findEnabledByChannelAndCategory(
            $channel, ConversionActionInterface::CATEGORY_PURCHASE
        );

        foreach ($conversionActions as $conversionAction) {
            $conversion = $this->conversionFactory->createFromOrder($order);
            $conversion->setName((string) $conversionAction->getName());
            $conversion->setCategory((string) $conversionAction->getCategory());
            $conversion->setGoogleClickId((string) $request->cookies->get($this->cookieName));
            $conversion->setChannel($channel);

            $manager = $this->getManager($conversion);
            $manager->persist($conversion);
        }

        if (isset($manager)) {
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
