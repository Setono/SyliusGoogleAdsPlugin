<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface ConversionInterface extends ResourceInterface, CodeAwareInterface, ToggleableInterface, ChannelsAwareInterface
{
    public const CATEGORY_PURCHASE = 'purchase';

    public const CATEGORY_ADD_TO_CART = 'add_to_cart';

    public const CATEGORY_BEGIN_CHECKOUT = 'begin_checkout';

    public const CATEGORY_SUBSCRIBE = 'subscribe';

    public const CATEGORY_SUBMIT_LEAD_FORM = 'submit_lead_form';

    public const CATEGORY_BOOK_APPOINTMENT = 'book_appointment';

    public const CATEGORY_SIGN_UP = 'sign_up';

    public const CATEGORY_REQUEST_QUOTE = 'request_quote';

    public const CATEGORY_GET_DIRECTIONS = 'get_directions';

    public const CATEGORY_OUTBOUND_CLICK = 'outbound_click';

    public const CATEGORY_CONTACT = 'contact';

    public const CATEGORY_PAGE_VIEW = 'page_view';

    public const CATEGORY_OTHER = 'other';

    public function getId(): ?int;

    /**
     * This is the category within the Google Ads interface
     */
    public function getCategory(): ?string;

    public function setCategory(string $category): void;

    public function getConversionId(): ?string;

    public function setConversionId(string $conversionId): void;

    public function getConversionLabel(): ?string;

    public function setConversionLabel(string $conversionLabel): void;
}
