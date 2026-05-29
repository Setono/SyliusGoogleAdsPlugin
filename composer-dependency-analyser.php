<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    // google/protobuf is used (e.g. Google\Protobuf\Internal\Message), but the analyser attributes those
    // symbols to the ext-protobuf extension, so it falsely reports the package as unused.
    ->ignoreErrorsOnPackage('google/protobuf', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/validator', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreUnknownClasses([\Setono\ClientBundle\Context\ClientContextInterface::class])
;
