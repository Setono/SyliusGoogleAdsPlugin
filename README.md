# Setono Sylius Google Ads Plugin

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

Plugin for tracking Google Ads related events and adding respective tags.

## Installation

### Step 1: Install required bundles

This plugin depends on two other bundles, so you have to install those first:

- Install [PHP templates bundle](https://github.com/Setono/PhpTemplatesBundle)
- Install [tag bag bundle](https://github.com/Setono/TagBagBundle)

### Step 2: Install and enable plugin

```bash
$ composer require setono/sylius-google-ads-plugin
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Add bundle to your `config/bundles.php`:

```php
<?php
# config/bundles.php

return [
    // ...
    Setono\SyliusGoogleAdsPlugin\SetonoSyliusGoogleAdsPlugin::class => ['all' => true],
    // ...
];

```

[ico-version]: https://poser.pugx.org/setono/sylius-google-ads-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-google-ads-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-google-ads-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/sylius-google-ads-plugin
[link-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/actions
