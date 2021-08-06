<?php

class EventListingHandler extends \Tomaj\NetteApi\Handlers\BaseHandler
{
    public function params()
    {
        return [
            new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST_JSON_KEY, 'key1', \Tomaj\NetteApi\Params\InputParam::OPTIONAL, null, true),
            new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST_JSON_KEY, 'key2', \Tomaj\NetteApi\Params\InputParam::REQUIRED),
        ];
    }
}

-----
<?php

class EventListingHandler extends \Tomaj\NetteApi\Handlers\BaseHandler
{
    public function params()
    {
        return [new \Tomaj\NetteApi\Params\JsonInputParam('json', '{
    "type": "object",
    "properties": {
        "key1": {
            "type": "array"
        },
        "key2": {
            "type": "string"
        }
    },
    "required": [
        "key2"
    ]
}')];
    }
}

