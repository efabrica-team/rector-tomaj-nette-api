<?php

use Rector\Core\Configuration\Option;
use Rector\TomajNetteApi\Rules\ChangeApiListingOnClickToCallback;
use Rector\TomajNetteApi\Rules\CreateApiConsoleControlRector;
use Rector\TomajNetteApi\Rules\CreateApiListingControlRector;
use Rector\TomajNetteApi\Rules\InputParamChangeRector;
use Rector\TomajNetteApi\Rules\PostJsonKeyToJsonInputParamChangeRector;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeDeclaration;
use Rector\TypeDeclaration\ValueObject\AddReturnTypeDeclaration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void
{
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTOLOAD_PATHS, __DIR__ . '/../Rules');

    $services = $containerConfigurator->services();

    $services->set(CreateApiListingControlRector::class);
    $services->set(CreateApiConsoleControlRector::class);
    $services->set(InputParamChangeRector::class);
    $services->set(PostJsonKeyToJsonInputParamChangeRector::class);
    $services->set(ChangeApiListingOnClickToCallback::class);

    $services->set(AddReturnTypeDeclarationRector::class)
        ->call('configure', [[
            AddReturnTypeDeclarationRector::METHOD_RETURN_TYPES => ValueObjectInliner::inline([
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Authorization\ApiAuthorizationInterface',
                    'authorized',
                    new BooleanType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Authorization\ApiAuthorizationInterface',
                    'getErrorMessage',
                    new UnionType([new StringType(), new NullType()])
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'params',
                    new ArrayType(new IntegerType(), new ObjectType('Tomaj\NetteApi\Params\ParamInterface'))
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'tags',
                    new ArrayType(new IntegerType(), new StringType())
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'outputs',
                    new ArrayType(new IntegerType(), new ObjectType('Tomaj\NetteApi\Output\OutputInterface'))
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'handle',
                    new ObjectType('Tomaj\NetteApi\Response\ResponseInterface')
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'description',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'summary',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'deprecated',
                    new BooleanType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    new BooleanType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Misc\BearerTokenRepositoryInterface',
                    'validToken',
                    new BooleanType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Misc\BearerTokenRepositoryInterface',
                    'ipRestrictions',
                    new UnionType([new StringType(), new NullType()])
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Misc\IpDetectorInterface',
                    'getRequestIp',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Params\ParamInterface',
                    'isValid',
                    new BooleanType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\Params\ParamInterface',
                    'getKey',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\EndpointInterface',
                    'getMethod',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\EndpointInterface',
                    'getVersion',
                    new IntegerType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\EndpointInterface',
                    'getPackage',
                    new StringType()
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\EndpointInterface',
                    'getApiAction',
                    new UnionType([new StringType(), new NullType()])
                ),
                new AddReturnTypeDeclaration(
                    'Tomaj\NetteApi\EndpointInterface',
                    'getUrl',
                    new StringType()
                ),
        ])
    ]]);

    $services->set(AddParamTypeDeclarationRector::class)
        ->call('configure', [[
            AddParamTypeDeclarationRector::PARAMETER_TYPEHINTS => ValueObjectInliner::inline([
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Handlers\ApiHandlerInterface',
                    'handle',
                    0,
                    new ArrayType(new StringType(), new MixedType())
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    0,
                    new IntegerType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    1,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    2,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    3,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    4,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    5,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Logger\ApiLoggerInterface',
                    'log',
                    6,
                    new IntegerType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Misc\BearerTokenRepositoryInterface',
                    'validToken',
                    0,
                    new StringType()
                ),
                new AddParamTypeDeclaration(
                    'Tomaj\NetteApi\Misc\BearerTokenRepositoryInterface',
                    'ipRestrictions',
                    0,
                    new StringType()
                ),

        ])
    ]]);

    $services->set(RenameMethodRector::class)->call('configure', [[
        RenameMethodRector::METHOD_CALL_RENAMES => ValueObjectInliner::inline([
            new MethodCallRename(
                'Tomaj\NetteApi\ApiDecider',
                'addApiHandler',
                'addApi'
            ),
            new MethodCallRename(
                'Tomaj\NetteApi\ApiDecider',
                'getApiHandler',
                'getApi'
            ),
        ]),
    ]]);
};
