<?php

$apiListing = new \Tomaj\NetteApi\Component\ApiListingControl($this, 'apiListingControl', $this->apiDecider);
$apiListing->onClick(function ($method, $version, $package, $apiAction) {
    $this->redirect('show', $method, $version, $package, $apiAction);
});
-----
<?php

$apiListing = new \Tomaj\NetteApi\Component\ApiListingControl($this, 'apiListingControl', $this->apiDecider);
$apiListing->onClick[] = function ($method, $version, $package, $apiAction) {
    $this->redirect('show', $method, $version, $package, $apiAction);
};
