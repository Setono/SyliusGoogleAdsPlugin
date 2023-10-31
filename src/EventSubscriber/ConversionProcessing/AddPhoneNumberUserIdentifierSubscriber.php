<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Google\Ads\GoogleAds\V13\Common\UserIdentifier;
use Google\Ads\GoogleAds\V13\Enums\UserIdentifierSourceEnum\UserIdentifierSource;
use Setono\SyliusGoogleAdsPlugin\Event\PreSetClickConversionUserIdentifiersEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddPhoneNumberUserIdentifierSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PreSetClickConversionUserIdentifiersEvent::class => 'add',
        ];
    }

    public function add(PreSetClickConversionUserIdentifiersEvent $event): void
    {
        $phoneNumber = $event->conversion->getOrder()?->getCustomer()?->getPhoneNumber() ?? $event->conversion->getOrder()?->getBillingAddress()?->getPhoneNumber();
        if (null === $phoneNumber) {
            return;
        }

        $countryCode = $event->conversion->getOrder()?->getBillingAddress()?->getCountryCode();
        if (null !== $countryCode) {
            return;
        }

        try {
            $phoneNumberParsed = PhoneNumber::parse($phoneNumber, $countryCode);
        } catch (\Throwable) {
            return;
        }

        $event->userIdentifiers[] = new UserIdentifier([
            'hashed_phone_number' => hash('sha256', $phoneNumberParsed->format(PhoneNumberFormat::E164)),
            'user_identifier_source' => UserIdentifierSource::FIRST_PARTY,
        ]);
    }
}
