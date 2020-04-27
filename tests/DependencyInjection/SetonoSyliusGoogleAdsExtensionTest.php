<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusGoogleAdsPlugin\DependencyInjection\SetonoSyliusGoogleAdsExtension;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusGoogleAdsExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusGoogleAdsExtension(),
        ];
    }

//    protected function getMinimalConfiguration(): array
//    {
//        return [
//            'option' => 'option_value',
//        ];
//    }
//
//    /**
//     * @test
//     */
//    public function after_loading_the_correct_parameter_has_been_set(): void
//    {
//        $this->load();
//
//        $this->assertContainerBuilderHasParameter('setono_sylius_google_ads.option', 'option_value');
//    }
}
