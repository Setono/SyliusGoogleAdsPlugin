<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Google\Ads\GoogleAds\V13\Common\UserIdentifier;
use Google\Ads\GoogleAds\V13\Enums\UserIdentifierSourceEnum\UserIdentifierSource;
use Setono\SyliusGoogleAdsPlugin\Event\PreSetClickConversionUserIdentifiersEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddEmailUserIdentifierSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PreSetClickConversionUserIdentifiersEvent::class => 'add',
        ];
    }

    public function add(PreSetClickConversionUserIdentifiersEvent $event): void
    {
        $email = $event->conversion->getOrder()?->getCustomer()?->getEmailCanonical();
        if (null === $email) {
            return;
        }

        $email = strtolower(trim($email));
        $emailParts = explode('@', $email);
        if (count($emailParts) !== 2) {
            return;
        }

        // Google Ads requires removal of any '.' characters preceding "gmail.com" or "googlemail.com".
        if (preg_match('/^(gmail|googlemail)\.com\s*/', $emailParts[1])) {
            $emailParts[0] = str_replace('.', '', $emailParts[0]);
            $email = sprintf('%s@%s', $emailParts[0], $emailParts[1]);
        }

        $event->userIdentifiers[] = new UserIdentifier([
            'hashed_email' => hash('sha256', $email),
            'user_identifier_source' => UserIdentifierSource::FIRST_PARTY,
        ]);
    }
}
