<?php

use Rector\Config\RectorConfig;
use Rector\TomajNetteApi\Rules\CreateApiListingControlRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(CreateApiListingControlRector::class);
};
