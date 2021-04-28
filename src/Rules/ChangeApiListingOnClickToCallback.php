<?php

namespace Rector\TomajNetteApi\Rules;

use PHPStan\Type\ObjectType;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ChangeApiListingOnClickToCallback extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->name) !== 'onClick') {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Tomaj\NetteApi\Component\ApiListingControl'))) {
            return null;
        }

        return new Assign(new Variable($node->var->name . '->onClick[]'), $node->args[0]->value);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes onClick(Closure) to ->onClick[] = Closure', [
            new CodeSample('TODO', 'TODO')
        ]);
    }
}
