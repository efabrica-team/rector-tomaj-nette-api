<?php

use Rector\TomajNetteApi\Rules\CreateApiListingControlRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CreateApiListingControlRector::class);
};
