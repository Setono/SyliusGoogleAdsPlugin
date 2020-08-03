<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Setono\TagBag\Tag\GtagConfig;
use Setono\TagBag\Tag\GtagLibrary;
use Setono\TagBag\Tag\TagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LibraryAndConfigSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'add',
        ];
    }

    public function add(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$this->isShopContext($request)) {
            return;
        }

        // Only add the library on 'real' page loads, not AJAX requests like add to cart
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $conversions = $this->getConversions();
        if (count($conversions) === 0) {
            return;
        }

        $firstConversion = $conversions[0];

        $this->tagBag->addTag(new GtagLibrary((string) $firstConversion->getConversionId()));

        foreach ($conversions as $conversion) {
            $this->tagBag->addTag(
                (new GtagConfig((string) $conversion->getConversionId()))
                    ->setSection(TagInterface::SECTION_HEAD)
                    ->addDependency(GtagLibrary::NAME)
            );
        }
    }
}
