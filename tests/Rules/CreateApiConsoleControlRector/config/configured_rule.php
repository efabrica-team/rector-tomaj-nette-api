<?php

use Rector\TomajNetteApi\Rules\CreateApiConsoleControlRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CreateApiConsoleControlRector::class);
};
