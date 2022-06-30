<?php

use Rector\Config\RectorConfig;
use Rector\TomajNetteApi\Rules\InputParamChangeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(InputParamChangeRector::class);
};
