# Setono Sylius Google Ads Plugin

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Quality Score][ico-code-quality]][link-code-quality]

Plugin for tracking Google Ads related events and adding respective tags.

## Installation

### Step 1: Install and enable plugin

Open a command console, enter your project directory and execute the following command to download the latest stable version of this plugin:

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

## Integration with Setonos Google Analytics plugin
If you're using the [Google Analytics plugin by Setono](https://github.com/Setono/SyliusAnalyticsPlugin) then this plugin
integrates seamlessly with that. No setup needed.

If you have made your own custom things with the Analytics plugin it may be that you don't want to use this integration,
and you can disable this by setting the `use_analytics_plugin` to `false` like so:

```yaml
# config/packages/setono_sylius_google_ads.yaml
setono_sylius_google_ads:
    use_analytics_plugin: false
```

[ico-version]: https://poser.pugx.org/setono/sylius-google-ads-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-google-ads-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-google-ads-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/workflows/build/badge.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusGoogleAdsPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-google-ads-plugin
[link-github-actions]: https://github.com/Setono/SyliusGoogleAdsPlugin/actions
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusGoogleAdsPlugin
