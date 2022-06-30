<?php

namespace Rector\TomajNetteApi\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class CreateApiListingControlRector extends AbstractRector
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
        if ($this->getName($node->class) !== 'Tomaj\NetteApi\Component\ApiListingControl') {
            return null;
        }

        if (!isset($node->getArgs()[2])) {
            return null;
        }

        $apiDeciderNode = $node->getArgs()[2]->value;
        return new New_(new FullyQualified('Tomaj\NetteApi\Component\ApiListingControl'), [new Arg($apiDeciderNode)]);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes for creating ApiListingControl', [
            new CodeSample(
                '$apiListing = new \Tomaj\NetteApi\Component\ApiListingControl($this, "apiListingControl", $this->apiDecider);',
                '$apiListing = new \Tomaj\NetteApi\Component\ApiListingControl($this->apiDecider);'
            )]);
    }
}
