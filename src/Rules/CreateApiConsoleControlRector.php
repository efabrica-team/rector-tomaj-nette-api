<?php

namespace Rector\TomajNetteApi\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class CreateApiConsoleControlRector extends AbstractRector
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
        if ($this->getName($node->class) !== 'Tomaj\NetteApi\Component\ApiConsoleControl') {
            return null;
        }

        $httpRequestNode = $node->getArgs()[0]->value;
        $endpointNode = $node->getArgs()[1]->value;
        $handlerNode = $node->getArgs()[2]->value;
        $authorizationNode = $node->getArgs()[3]->value;

        if ($endpointNode instanceof ArrayDimFetch && $handlerNode instanceof ArrayDimFetch && $authorizationNode instanceof ArrayDimFetch) {
            return new New_(new FullyQualified('Tomaj\NetteApi\Component\ApiConsoleControl'), [
                new Arg($httpRequestNode),
                new Arg(new MethodCall($endpointNode->var, 'getEndpoint')),
                new Arg(new MethodCall($handlerNode->var, 'getHandler')),
                new Arg(new MethodCall($authorizationNode->var, 'getAuthorization')),
            ]);
        }
        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes for creating ApiConsoleControl', [
            new CodeSample(
                '
$api = $this->apiDecider->getApiHandler($this->params["method"], $this->params["version"], $this->params["package"], isset($this->params["apiAction"]) ? $this->params["apiAction"] : null);
$apiConsole = new \Tomaj\NetteApi\Component\ApiConsoleControl($this->getHttpRequest(), $api["endpoint"], $api["handler"], $api["authorization"]);',
                '
$api = $this->apiDecider->getApiHandler($this->params["method"], $this->params["version"], $this->params["package"], isset($this->params["apiAction"]) ? $this->params["apiAction"] : null);
$apiConsole = new \Tomaj\NetteApi\Component\ApiConsoleControl($this->getHttpRequest(), $api->getEndpoint(), $api->getHandler(), $api->getAuthorization());'
            )]);
    }
}
