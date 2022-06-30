<?php

use Rector\Config\RectorConfig;
use Rector\TomajNetteApi\Rules\PostJsonKeyToJsonInputParamChangeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(PostJsonKeyToJsonInputParamChangeRector::class);
};
