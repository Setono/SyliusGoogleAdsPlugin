# Sylius plugin for Google Ads

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

This plugin tracks conversions in your Sylius store. It's done with the [Google Ads API](https://developers.google.com/google-ads/api/docs/start)
instead of the default javascript tracking. It has a few benefits to do this:
- Easier to control the consent status for a given user
- Easier to change the value of an order after the fact
- No javascripts on your page to track Google Ads, which means faster page load
- You decide the ttl on your cookies, not Apple and their ITP
- No risk of losing tracking because of ad blockers

## Installation

### Step 1: Install gRPC

Internally this plugin uses the [google-ads-php](https://github.com/googleads/google-ads-php). To use that library properly
it's advised to install the gRPC PHP extension. It should work by just running `pecl install grpc` and enabling the extension
in your `php.ini` by adding `extension=grpc.so`.

### Step 2: Install and enable plugin

```bash
composer require setono/sylius-google-ads-plugin
```

Add the bundle to your `config/bundles.php` before the `SyliusGridBundle`:

```php
<?php
# config/bundles.php

return [
    // ...
    
    Setono\SyliusGoogleAdsPlugin\SetonoSyliusGoogleAdsPlugin::class => ['all' => true], // Added before the grid bundle
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    
    // ...
];
```

### Step 3: Add configuration
```yaml
# config/packages/setono_sylius_google_ads.yaml
imports:
    - "@SetonoSyliusGoogleAdsPlugin/Resources/config/app/config.yaml"
```

```yaml
# config/routes/setono_sylius_google_ads.yaml
setono_sylius_google_ads:
    resource: "@SetonoSyliusGoogleAdsPlugin/Resources/config/routes.yaml"
```

### Step 4: Create migration file
```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Step 5: Set up cronjobs

The first cronjob will process Google Ads conversions. Run this cronjob regularly, e.g. every 5 minutes:

```shell
php bin/console setono:sylius-google-ads:process-conversions
```

The next cronjob will prune the conversions table. Run this job as often as you'd like, maybe daily:

```shell
php bin/console setono:sylius-google-ads:prune-conversions
```

### Step 6: Map the Messenger command to an async transport (optional, but recommended)

The plugin uses the Symfony Messenger to dispatch a message (`ProcessConversion`) which will trigger the processing
of a conversion. If you want to do this asynchronously, you can do something like the following in your messenger config:

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'Setono\SyliusGoogleAdsPlugin\Message\Command\CommandInterface': async
```

This maps all messages implementing that interface to the `async` transport.

Now the plugin is installed. Please read the next section to learn how to use it in your store.

## Usage

To start using the plugin, go to https://your-domain.com/admin/google-ads and follow the instructions.

[ico-version]: https://poser.pugx.org/setono/sylius-google-ads-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-google-ads-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/sylius-google-ads-plugin
[link-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/actions
