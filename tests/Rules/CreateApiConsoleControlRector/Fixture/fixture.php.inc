<?php

$api = $this->apiDecider->getApiHandler($this->params['method'], $this->params['version'], $this->params['package'], isset($this->params['apiAction']) ? $this->params['apiAction'] : null);
$apiConsole = new \Tomaj\NetteApi\Component\ApiConsoleControl($this->getHttpRequest(), $api['endpoint'], $api['handler'], $api['authorization']);
-----
<?php

$api = $this->apiDecider->getApiHandler($this->params['method'], $this->params['version'], $this->params['package'], isset($this->params['apiAction']) ? $this->params['apiAction'] : null);
$apiConsole = new \Tomaj\NetteApi\Component\ApiConsoleControl($this->getHttpRequest(), $api->getEndpoint(), $api->getHandler(), $api->getAuthorization());
