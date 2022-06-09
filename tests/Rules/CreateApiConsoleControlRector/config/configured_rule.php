<?php

use Rector\Config\RectorConfig;
use Rector\TomajNetteApi\Rules\CreateApiConsoleControlRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(CreateApiConsoleControlRector::class);
};
