<?php

namespace Rector\TomajNetteApi\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class InputParamChangeRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->class) !== 'Tomaj\NetteApi\Params\InputParam') {
            return null;
        }

        $typeNode = $node->args[0]->value ?? null;
        if (!$typeNode) {
            return null;
        }

        $newClassName = null;
        if ($typeNode instanceof ClassConstFetch) {
            $name = $this->getName($typeNode);
            if ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_GET') {
                $newClassName = 'Tomaj\NetteApi\Params\GetInputParam';
            } elseif ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_POST') {
                $newClassName = 'Tomaj\NetteApi\Params\PostInputParam';
            } elseif ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_PUT') {
                $newClassName = 'Tomaj\NetteApi\Params\PutInputParam';
            } elseif ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_FILE') {
                $newClassName = 'Tomaj\NetteApi\Params\FileInputParam';
            } elseif ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_COOKIE') {
                $newClassName = 'Tomaj\NetteApi\Params\CookieInputParam';
            } elseif ($name === 'Tomaj\NetteApi\Params\InputParam::TYPE_POST_RAW') {
                $newClassName = 'Tomaj\NetteApi\Params\RawInputParam';
            }
        } elseif ($typeNode instanceof String_) {
            $value = $typeNode->value;
            if ($value === 'GET') {
                $newClassName = 'Tomaj\NetteApi\Params\GetInputParam';
            } elseif ($value === 'POST') {
                $newClassName = 'Tomaj\NetteApi\Params\PostInputParam';
            } elseif ($value === 'PUT') {
                $newClassName = 'Tomaj\NetteApi\Params\PutInputParam';
            } elseif ($value === 'FILE') {
                $newClassName = 'Tomaj\NetteApi\Params\FileInputParam';
            } elseif ($value === 'COOKIE') {
                $newClassName = 'Tomaj\NetteApi\Params\CookieInputParam';
            } elseif ($value === 'TYPE_POST_RAW') {
                $newClassName = 'Tomaj\NetteApi\Params\RawInputParam';
            }
        }

        if (!$newClassName) {
            return null;
        }

        $nameNode = $node->args[1]->value ?? null;
        if (!$nameNode) {
            return null;
        }

        $outputNode = new New_(new FullyQualified($newClassName), [new Arg($nameNode)]);

        // transform required
        $isRequired = false;
        $isRequiredNode = $node->args[2]->value ?? null;
        if ($isRequiredNode instanceof ClassConstFetch) {
            $name = $this->getName($isRequiredNode);
            if ($name === 'Tomaj\NetteApi\Params\InputParam::REQUIRED') {
                $isRequired = true;
            }
        } elseif ($isRequiredNode instanceof ConstFetch) {
            $value = $this->getName($isRequiredNode);
            if ($value === 'true') {
                $isRequired = true;
            }
        }
        if ($isRequired) {
            $outputNode = new MethodCall($outputNode, 'setRequired');
        }

        // transform available values
        $availableValuesNode = $node->args[3] ?? null;
        if ($availableValuesNode && $availableValuesNode->value instanceof ConstFetch) {
            $availableValuesNodeValue = $this->getName($availableValuesNode->value);
            if ($availableValuesNodeValue === 'null') {
                $availableValuesNode = null;
            }
        }
        if ($availableValuesNode) {
            $outputNode = new MethodCall($outputNode, 'setAvailableValues', [$availableValuesNode]);
        }

        // transform is multi
        $isMulti = false;
        $isMultiNode = $node->args[4]->value ?? null;
        if ($isMultiNode instanceof ConstFetch) {
            $value = $this->getName($isMultiNode);
            if ($value === 'true') {
                $isMulti = true;
            }
        }
        if ($isMulti) {
            $outputNode = new MethodCall($outputNode, 'setMulti');
        }

        return $outputNode;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes InputParam to conrete InputParam type', [
            new CodeSample('public function params()
{
    return [
        new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_GET, "get_param", \Tomaj\NetteApi\Params\InputParam::OPTIONAL, null, true),
        new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST, "post_param", \Tomaj\NetteApi\Params\InputParam::REQUIRED),
        new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_GET, "types", \Tomaj\NetteApi\Params\InputParam::REQUIRED, ["type1" => "Type 1", "type2" => "Type 2"]),
    ];
}', 'public function params()
{
    return [
        (new \Tomaj\NetteApi\Params\GetInputParam("get_param"))->setMulti(),
        (new \Tomaj\NetteApi\Params\PostInputParam("post_param"))->setRequired(),
        (new \Tomaj\NetteApi\Params\GetInputParam("types"))->setRequired()->setAvailableValues(["type1" => "Type 1", "type2" => "Type 2"]),
    ];
}'
            )]
        );
    }
}
