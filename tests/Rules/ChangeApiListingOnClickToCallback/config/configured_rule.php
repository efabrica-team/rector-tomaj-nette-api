<?php

use Rector\Config\RectorConfig;
use Rector\TomajNetteApi\Rules\ChangeApiListingOnClickToCallbackRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ChangeApiListingOnClickToCallbackRector::class);
};
