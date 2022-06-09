<?php

namespace Rector\TomajNetteApi\Rules;

use PHPStan\Type\ObjectType;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Unset_;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class PostJsonKeyToJsonInputParamChangeRector extends AbstractRector
{
    /** @var array<string, array<string, array{type: string, available_values: ?array<string|int>, required: bool}>>  */
    private static array $parameters = [];

    private bool $handleProcessed = false;

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        $parentNode = $node->getAttribute(AttributeKey::PARENT_NODE);
        if (!$parentNode instanceof ClassLike || !$this->isObjectType($parentNode, new ObjectType('Tomaj\NetteApi\Handlers\ApiHandlerInterface'))) {
            return null;
        }

        if ($parentNode->name === null) {
            return null;
        }

        $className = $this->getName($parentNode->name);
        $methodName = $this->getName($node->name);
        if ($methodName === 'params') {
            $parameters = self::$parameters[$className] ?? null;
            if ($parameters) {
                return null;
            }
            $stmts = $node->stmts;
            if (!is_array($stmts)) {
                return null;
            }

            $subNodes = [
                'flags' => $node->flags,
                'byRef' => $node->byRef,
                'params' => $node->params,
                'returnType' => $node->returnType,
                'stmts' => $stmts,
            ];

            foreach ($stmts as $stmt) {
                if ($stmt instanceof Return_) {
                    $returnExpression = $stmt->expr;

                    if (!$returnExpression instanceof Array_) {
                        return null;
                    }

                    $items = [];
                    /** @var ArrayItem $returnItem */
                    foreach ($returnExpression->items as $returnItem) {
                        $returnItemValue = $returnItem->value;
                        if (!$returnItemValue instanceof New_) {
                            $items[] = $returnItem;
                            continue;
                        }

                        if ($this->getName($returnItemValue->class) !== 'Tomaj\NetteApi\Params\InputParam') {
                            $items[] = $returnItem;
                            continue;
                        }

                        $typeNode = $returnItemValue->getArgs()[0]->value ?? null;
                        if (!$typeNode) {
                            $items[] = $returnItem;
                            continue;
                        }

                        if ($typeNode instanceof ClassConstFetch) {
                            $name = $this->getName($typeNode);
                            if ($name !== 'Tomaj\NetteApi\Params\InputParam::TYPE_POST_JSON_KEY') {
                                $items[] = $returnItem;
                                continue;
                            }
                        } elseif ($typeNode instanceof String_) {
                            $value = $typeNode->value;
                            if ($value !== 'POST_JSON_KEY') {
                                $items[] = $returnItem;
                                continue;
                            }
                        }

                        $paramName = $returnItemValue->getArgs()[1]->value ?? null;
                        if ($paramName === null) {
                            $items[] = $returnItem;
                            continue;
                        }

                        if (!$paramName instanceof String_) {
                            $items[] = $returnItem;
                            continue;
                        }

                        $type = 'string';
                        $availableValues = null;

                        $isRequired = false;
                        $isRequiredNode = $returnItemValue->getArgs()[2]->value ?? null;
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

                        $availableValuesNode = $returnItemValue->getArgs()[3] ?? null;
                        if ($availableValuesNode) {
                            $availableValuesNodeValue = $this->getName($availableValuesNode->value);
                            if ($availableValuesNodeValue !== 'null' && $availableValuesNode->value instanceof Array_) {
                                $availableValues = [];
                                /** @var ArrayItem $item */
                                foreach ($availableValuesNode->value->items as $item) {
                                    if ($item->value instanceof String_ || $item->value instanceof LNumber) {
                                        $availableValues[] = $item->value->value;
                                    }
                                }
                            }
                        }

                        $isMultiNode = $returnItemValue->getArgs()[4]->value ?? null;
                        if ($isMultiNode instanceof ConstFetch) {
                            $value = $this->getName($isMultiNode);
                            if ($value === 'true') {
                                $type = 'array';
                            }
                        }

                        self::$parameters[$className][$paramName->value] = [
                            'type' => $type,
                            'available_values' => $availableValues,
                            'required' => $isRequired,
                        ];
                    }

                    if (isset(self::$parameters[$className])) {
                        $schema = [
                            'type' => 'object',
                        ];
                        $properties = [];
                        $required = [];
                        foreach (self::$parameters[$className] as $parameter => $parameterSettings) {
                            $properties[$parameter] = [
                                'type' => $parameterSettings['type'],
                            ];
                            if ($parameterSettings['available_values']) {
                                $properties[$parameter]['enum'] = $parameterSettings['available_values'];
                            }
                            if ($parameterSettings['required']) {
                                $required[] = $parameter;
                            }
                        }
                        $schema['properties'] = $properties;
                        if ($required) {
                            $schema['required'] = $required;
                        }

                        $items[] = new ArrayItem(new New_(
                            new FullyQualified('Tomaj\NetteApi\Params\JsonInputParam'),
                            [new Arg(new String_('json')), new Arg(new String_(json_encode($schema, JSON_PRETTY_PRINT) ?: '{}'))]
                        ));
                    }

                    $returnExpression->items = $items;
                }
            }

            return new ClassMethod($methodName, $subNodes, $node->getAttributes());
        }

        if (!$this->handleProcessed && $methodName === 'handle') {
            $this->handleProcessed = true;
            $parameters = self::$parameters[$className] ?? null;
            if (!$parameters) {
                return null;
            }

            $stmts = $node->stmts;
            if (!is_array($stmts)) {
                return null;
            }

            $newStatements = [
                // assign $params['json'] to $json
                new Expression(
                    new Assign(
                        new Variable('json'),
                        new Variable('params[\'json\']')
                    )
                ),
                // unset $params['json']
                new Unset_([new Variable('params[\'json\']')]),
            ];

            foreach ($parameters as $parameter => $parameterSettings) {
                $sourceValue = new Variable('json[\'' . $parameter . '\']');
                if (!$parameterSettings['required']) {
                    $sourceValue = new Coalesce($sourceValue, new ConstFetch(new Name('null')));
                }

                $newStatements[] = new Expression(
                    new Assign(
                        new Variable('params[\'' . $parameter . '\']'),
                        $sourceValue
                    )
                );
            }

            $stmts = array_merge($newStatements, $stmts);

            $subNodes = [
                'flags' => $node->flags,
                'byRef' => $node->byRef,
                'params' => $node->params,
                'returnType' => $node->returnType,
                'stmts' => $stmts,
            ];

            return new ClassMethod($methodName, $subNodes, $node->getAttributes());
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes all InputParam::TYPE_POST_JSON_KEY to one JsonInputParam', [
                new CodeSample('public function params()
{
    return [
        new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST_JSON_KEY, \'key1\', \Tomaj\NetteApi\Params\InputParam::OPTIONAL, null, true),
        new \Tomaj\NetteApi\Params\InputParam(\Tomaj\NetteApi\Params\InputParam::TYPE_POST_JSON_KEY, \'key2\', \Tomaj\NetteApi\Params\InputParam::REQUIRED),
        
    ];
}', 'public function params()
{
    return [
        (new \Tomaj\NetteApi\Params\JsonInputParam(\'json\', \'{"type":"object","properties":["key1":{"type":"array","key2":{"type":"string"}],"required":["key2"]}\')),
    ];
}')
            ]);
    }
}
