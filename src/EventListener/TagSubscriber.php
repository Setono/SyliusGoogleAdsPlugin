<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class TagSubscriber implements EventSubscriberInterface
{
    protected TagBagInterface $tagBag;

    private ChannelContextInterface $channelContext;

    private ConversionRepositoryInterface $conversionRepository;

    protected EventDispatcherInterface $eventDispatcher;

    /**
     * We make this nullable because we then have the opportunity to check if the conversions were tried fetched
     * from the database
     */
    private ?array $conversions = null;

    private RequestStack $requestStack;

    private FirewallMap $firewallMap;

    public function __construct(
        TagBagInterface $tagBag,
        ChannelContextInterface $channelContext,
        ConversionRepositoryInterface $conversionRepository,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FirewallMap $firewallMap
    ) {
        $this->tagBag = $tagBag;
        $this->channelContext = $channelContext;
        $this->conversionRepository = $conversionRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
    }

    /**
     * @return ConversionInterface[]
     */
    protected function getConversions(): array
    {
        if (null === $this->conversions) {
            $this->conversions = $this->conversionRepository->findEnabledByChannel($this->channelContext->getChannel());
        }

        return $this->conversions;
    }

    /**
     * @return ConversionInterface[]
     */
    protected function getConversionsByCategory(string $category): array
    {
        return array_filter($this->getConversions(), static function (ConversionInterface $conversion) use ($category): bool {
            return $conversion->getCategory() === $category;
        });
    }

    protected function isShopContext(Request $request = null): bool
    {
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
            if (null === $request) {
                return true;
            }
        }

        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if (null === $firewallConfig) {
            return true;
        }

        return $firewallConfig->getName() === 'shop';
    }

    protected function formatMoney(int $money): float
    {
        return round($money / 100, 2);
    }
}
