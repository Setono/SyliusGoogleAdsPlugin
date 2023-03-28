<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $subMenu = $menu->getChild('marketing');

        if (null !== $subMenu) {
            $this->addChild($subMenu);
        } else {
            $this->addChild($menu->getFirstChild());
        }
    }

    private function addChild(ItemInterface $item): void
    {
        $item
            ->addChild('google_ads', [
                'route' => 'setono_sylius_google_ads_admin_conversion_action_index',
            ])
            ->setLabel('setono_sylius_google_ads.ui.google_ads')
            ->setLabelAttribute('icon', 'money bill alternate outline')
        ;
    }
}
