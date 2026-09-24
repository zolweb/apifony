<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Printer\Printer;
use Zol\Apifony\Resolved\Operation;

class Action
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $route,
        string $method,
        Operation $operation,
    ): self {
        $className = Naming::forMember($operation->operationId);

        return new self(
            $className,
            $route,
            $method,
            self::buildParameters($bundleNamespace, $aggregateName, $className, $operation),
            self::buildRequestBody($bundleNamespace, $aggregateName, $className, $operation),
            self::buildResponses($bundleNamespace, $aggregateName, $className, $operation),
        );
    }

    /**
     * @param list<ActionParameter> $parameters
     * @param list<ActionResponse>  $responses
     */
    private function __construct(
        private readonly string $name,
        private readonly string $route,
        private readonly string $method,
        private readonly array $parameters,
        private readonly ?ActionRequestBody $requestBody,
        private readonly array $responses,
    ) {
    }

    public function getServiceName(): string
    {
        return Naming::forServiceId($this->name);
    }

    public function getRequestBody(): ?ActionRequestBody
    {
        return $this->requestBody;
    }

    /**
     * @param list<string> $in
     *
     * @return list<ActionParameter>
     */
    public function getParameters(array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_values(
            array_filter(
                $this->parameters,
                static fn (ActionParameter $param) => \in_array($param->getIn(), $in, true),
            ),
        );
    }

    /**
     * @return list<ActionResponse>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @return list<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->parameters as $parameter) {
            foreach ($parameter->getModels() as $model) {
                $files[] = $model;
            }
        }
        foreach ($this->getResponses() as $response) {
            $files[] = $response;
        }
        foreach ($this->requestBody?->getPayloadModels() ?? [] as $payloadModel) {
            $files[] = $payloadModel;
        }
        foreach ($this->getResponses() as $response) {
            foreach ($response->getPayloadModels() as $payloadModel) {
                $files[] = $payloadModel;
            }
        }

        return $files;
    }

    /**
     * @return list<ActionParameter>
     *
     * @throws Exception
     */
    private static function buildParameters(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
    ): array {
        $ordinal = 0;
        $parameters = [];
        foreach ($operation->parameters as $parameter) {
            $parameters[] = ActionParameter::build($bundleNamespace, $aggregateName, $actionClassName, $parameter, ++$ordinal);
        }

        usort(
            $parameters,
            static fn (ActionParameter $param1, ActionParameter $param2): int => $param1->shouldBePositionedBefore($param2) ? -1 : 1
        );

        $variableNames = [];
        foreach ($parameters as $parameter) {
            if (isset($variableNames[$parameter->getVariableName()])) {
                throw new Exception(\sprintf('Parameters \'%s\' and \'%s\' both map to the \'$%s\' handler argument.', $variableNames[$parameter->getVariableName()], $parameter->getRawName(), $parameter->getVariableName()), $operation->path);
            }
            $variableNames[$parameter->getVariableName()] = $parameter->getRawName();
        }

        return $parameters;
    }

    /**
     * @throws Exception
     */
    private static function buildRequestBody(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
    ): ?ActionRequestBody {
        $requestBody = $operation->requestBody;
        if ($requestBody === null || \count($requestBody->content) === 0) {
            return null;
        }
        if (\count(array_diff_key($requestBody->content, ['application/json' => null])) > 0) {
            throw new Exception('Only application/json is supported by Apifony for response bodies.', $requestBody->path);
        }

        // Due to StopLight not allowing to declare required on requestBodies, a requestBody is always required
        return ActionRequestBody::build(
            $bundleNamespace,
            $aggregateName,
            $actionClassName,
            $requestBody->content['application/json'],
        );
    }

    /**
     * @return list<ActionResponse>
     *
     * @throws Exception
     */
    private static function buildResponses(
        string $bundleNamespace,
        string $aggregateName,
        string $className,
        Operation $operation,
    ): array {
        $responses = [];
        if ($operation->responses !== null) {
            foreach ($operation->responses->responses as $code => $response) {
                if (\in_array($code, ['1XX', '2XX', '3XX', '4XX', '5XX'], true)) {
                    throw new Exception('HTTP status code ranges are not supported by Apifony.', $response->path);
                }
                if (\count(array_diff_key($response->content, ['application/json' => null])) > 0) {
                    throw new Exception('Only application/json is supported by Apifony for response bodies.', $response->path);
                }
                $responses[] = ActionResponse::build(
                    $bundleNamespace,
                    $aggregateName,
                    $className,
                    (int) $code,
                    $response,
                    \array_key_exists('application/json', $response->content)
                        ? $response->content['application/json']->schema : null,
                );
            }
        }

        return $responses;
    }

    public function getRequestBodyPayloadTypeName(): Name
    {
        $type = $this->requestBody?->getPayloadTypeName();

        if ($type === null) {
            throw new \RuntimeException();
        }

        return $type;
    }

    /**
     * @return array{path: string, methods: string, controller: string, requirements?: array<string, string>}
     */
    public function getRoute(string $controllerClassName): array
    {
        $route = [
            'path' => $this->route,
            'methods' => $this->method,
            'controller' => "{$controllerClassName}::{$this->name}",
        ];

        if (\count($this->getParameters(['path'])) > 0) {
            $route['requirements'] = [];
            foreach ($this->getParameters(['path']) as $parameter) {
                $route['requirements'][$parameter->getRawName()] = $parameter->getRouteRequirementPattern();
            }
        }

        return $route;
    }

    /**
     * @return list<ClassMethod>
     *
     * @throws Exception
     */
    public function getParameterDenormalizerMethods(): array
    {
        $context = new DenormalizationContext(DenormalizationContext::SOURCE_QUERY);

        $methods = [];
        foreach ($this->parameters as $parameter) {
            $method = $parameter->getDenormalizerMethod($context);
            if ($method !== null) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * Populates the registries with every model this action denormalizes, so that the
     * AbstractController can emit one method per model for the whole bundle.
     *
     * @throws Exception
     */
    public function registerDenormalizationModels(DenormalizationContext $queryContext, DenormalizationContext $jsonContext): void
    {
        foreach ($this->parameters as $parameter) {
            $parameter->registerDenormalizationModels($queryContext);
        }

        $this->requestBody?->registerDenormalizationModels($jsonContext);
    }

    /**
     * Whether the generated controller renders at least one Assert\* constraint, and therefore
     * needs the alias imported.
     *
     * @throws Exception
     */
    public function usesAssertConstraints(): bool
    {
        $constraints = $this->requestBody?->getResidualConstraints() ?? [];
        foreach ($this->parameters as $parameter) {
            foreach ($parameter->getResidualConstraints() as $constraint) {
                $constraints[] = $constraint;
            }
        }

        foreach ($constraints as $constraint) {
            if (str_starts_with($constraint->getName(), 'Assert\\')) {
                return true;
            }
        }

        return false;
    }

    public function getClassMethod(): ClassMethod
    {
        $f = new BuilderFactory();

        $actionMethod = $f->method($this->name)
            ->makePublic()
            ->addParam($f->param('request')->setType('Request'))
        ;

        foreach ($this->getParameters(['path']) as $parameter) {
            $actionMethod->addParam($parameter->asParam(true));
        }

        $actionMethod->setReturnType('Response');

        $actionMethod->addStmt(new Expression(new Assign($f->var('errors'), new Array_([], ['kind' => Array_::KIND_SHORT]))));

        foreach ($this->getParameters(['path']) as $parameter) {
            $actionMethod->addStmts($parameter->getPathSanitizationStmts());
        }

        foreach ($this->getParameters(['query', 'header', 'cookie']) as $parameter) {
            $actionMethod->addStmts($parameter->getNonPathSanitizationStmts());
        }

        if ($this->requestBody !== null) {
            $actionMethod->addStmts($this->requestBody->getStmts());
        }

        $actionMethod->addStmt(new If_(new Greater($f->funcCall('\count', [$f->var('errors')]), $f->val(0)), ['stmts' => [
            new Return_($f->new('JsonResponse', [
                new Array_([
                    new ArrayItem($f->val('validation_failed'), $f->val('code')),
                    new ArrayItem($f->val('Validation has failed.'), $f->val('message')),
                    new ArrayItem($f->var('errors'), $f->val('errors')),
                ], ['kind' => Array_::KIND_SHORT]),
                $f->classConstFetch('Response', 'HTTP_BAD_REQUEST'),
            ])),
        ]]))
            ->addStmt(new Expression(new Assign($f->var('response'), $f->methodCall($f->propertyFetch($f->var('this'), 'handler'), $this->name, array_merge(
                array_map(static fn (ActionParameter $parameter): Variable => $parameter->asVariable(), $this->parameters),
                $this->requestBody !== null ? [$f->var('requestBodyPayload')] : [],
            )))))
            ->addStmt(new If_(new Identical($f->methodCall($f->var('response'), 'getContentType'), $f->val('application/json')), ['stmts' => [
                new Return_($f->new('JsonResponse', [$f->propertyFetch($f->var('response'), 'payload'), $f->methodCall($f->var('response'), 'getCode'), $f->methodCall($f->var('response'), 'getHeaders')])),
            ]]))
            ->addStmt(new Return_($f->new('Response', [$f->val(''), $f->methodCall($f->var('response'), 'getCode'), $f->methodCall($f->var('response'), 'getHeaders')])))
        ;

        return $actionMethod->getNode();
    }

    public function getHandlerMethod(): ClassMethod
    {
        $f = new BuilderFactory();

        $method = $f->method($this->name)
            ->makePublic()
            ->addParams(array_merge(
                array_map(
                    static fn (ActionParameter $param): Param => $param->asParam(),
                    $this->parameters,
                ),
                $this->requestBody !== null ? [$f->param('requestBodyPayload')->setType($this->getRequestBodyPayloadTypeName())] : [],
            ))
            ->setReturnType(new UnionType(array_map(
                static fn (ActionResponse $response) => new Name($response->getClassName()),
                $this->responses,
            )))
        ;

        $docTagNodes = [];
        foreach ($this->parameters as $parameter) {
            $docTagNode = $parameter->getDocTagNode();
            if ($docTagNode !== null) {
                $docTagNodes[] = $docTagNode;
            }
        }
        if (\count($docTagNodes) > 0) {
            $method->setDocComment((new Printer())->print(new PhpDocNode($docTagNodes)));
        }

        return $method->getNode();
    }
}
