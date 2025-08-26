<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Setono\SyliusGoogleAdsPlugin\Provisioner\CustomerListProvisionerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @experimental
 */
final class ProvisionCustomerListsSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CustomerListProvisionerInterface $customerListProvisioner)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'provision',
        ];
    }

    public function provision(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if ('setono_sylius_google_ads_admin_customer_list_index' !== $route) {
            return;
        }

        $this->customerListProvisioner->provision();
    }
}
