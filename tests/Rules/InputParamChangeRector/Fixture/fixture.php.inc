<?php

[
    new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_GET, 'get_param', \Tomaj\NetteApi\Params\InputParam::OPTIONAL, null, true),
    new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST, 'post_param', \Tomaj\NetteApi\Params\InputParam::REQUIRED),
    new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_GET, 'types', \Tomaj\NetteApi\Params\InputParam::REQUIRED, ['type1' => 'Type 1', 'type2' => 'Type 2']),
];
-----
<?php

[
    (new \Tomaj\NetteApi\Params\GetInputParam('get_param'))->setMulti(),
    (new \Tomaj\NetteApi\Params\PostInputParam('post_param'))->setRequired(),
    (new \Tomaj\NetteApi\Params\GetInputParam('types'))->setRequired()->setAvailableValues(['type1' => 'Type 1', 'type2' => 'Type 2']),
];
