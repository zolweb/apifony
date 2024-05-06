<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Operation;
use Zol\Ogen\OpenApi\Reference;

use function Symfony\Component\String\u;

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
        ?Components $components,
    ): self {
        return new self(
            $className = u($operation->operationId)->camel()->toString(),
            $route,
            $method,
            $parameters = self::buildParameters($className, $operation, $components),
            $requestBodies = self::buildRequestBodies($bundleNamespace, $aggregateName, $className, $operation, $components),
            $responseContentTypes = self::buildResponseContentTypes($operation, $components),
            self::buildCases(
                $bundleNamespace,
                $aggregateName,
                $className,
                $operation,
                $components,
                $parameters,
                self::buildRequestBodyPayloadTypes($requestBodies),
                $responseContentTypes,
            ),
        );
    }

    /**
     * @param array<ActionParameter>   $parameters
     * @param array<ActionRequestBody> $requestBodies
     * @param array<?string>           $responseContentTypes
     * @param array<ActionCase>        $cases
     */
    private function __construct(
        private readonly string $name,
        private readonly string $route,
        private readonly string $method,
        private readonly array $parameters,
        private readonly array $requestBodies,
        private readonly array $responseContentTypes,
        private readonly array $cases,
    ) {
    }

    public function getServiceName(): string
    {
        return u($this->name)->snake()->toString();
    }

    /**
     * @param array<string> $in
     *
     * @return array<ActionParameter>
     */
    public function getParameters(array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_filter(
            $this->parameters,
            static fn (ActionParameter $param) => \in_array($param->getIn(), $in, true),
        );
    }

    /**
     * @return array<ActionRequestBody>
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @return array<ActionCase>
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->cases as $case) {
            foreach ($case->getResponses() as $response) {
                $files[] = $response;
            }
        }
        foreach ($this->requestBodies as $requestBody) {
            foreach ($requestBody->getPayloadModels() as $payloadModel) {
                $files[] = $payloadModel;
            }
        }
        foreach ($this->cases as $case) {
            foreach ($case->getResponses() as $response) {
                foreach ($response->getPayloadModels() as $payloadModel) {
                    $files[] = $payloadModel;
                }
            }
        }

        return $files;
    }

    /**
     * @return array<ActionParameter>
     *
     * @throws Exception
     */
    private static function buildParameters(
        string $actionClassName,
        Operation $operation,
        ?Components $components,
    ): array {
        $ordinal = 0;
        $parameters = [];
        foreach ($operation->parameters as $parameter) {
            if ($parameter instanceof Reference) {
                if ($components === null || !isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('Reference not found in parameters components.');
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $parameters[] = ActionParameter::build($actionClassName, $parameter, $components, ++$ordinal);
        }

        usort(
            $parameters,
            static fn (ActionParameter $param1, ActionParameter $param2): int => $param1->shouldBePositionedBefore($param2) ? -1 : 1
        );

        return $parameters;
    }

    /**
     * @return array<ActionRequestBody>
     *
     * @throws Exception
     */
    private static function buildRequestBodies(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        ?Components $components,
    ): array {
        $requestBodies = [];

        $requestBody = $operation->requestBody;
        if ($requestBody instanceof Reference) {
            if ($components === null || !isset($components->requestBodies[$requestBody->getName()])) {
                throw new Exception('Reference not found in requestBodies components.');
            }
            $requestBody = $components->requestBodies[$requestBody->getName()];
        }

        // Due to StopLight not allowing to declare required on requestBodies, a requestBody is always required
        if ($requestBody === null || \count($requestBody->content) === 0 /* || !$requestBody->required */) {
            $requestBodies[] = ActionRequestBody::build(
                $bundleNamespace,
                $aggregateName,
                $actionClassName,
                null,
                null,
                $components,
            );
        }

        foreach ($requestBody->content ?? [] as $mimeType => $mediaType) {
            if ($mediaType->schema === null) {
                throw new Exception('MediaTypes without schema are not supported.');
            }
            $requestBodies[] = ActionRequestBody::build(
                $bundleNamespace,
                $aggregateName,
                $actionClassName,
                $mimeType,
                $mediaType,
                $components,
            );
        }

        return $requestBodies;
    }

    /**
     * @param array<ActionRequestBody> $requestBodies
     *
     * @return array<?Type>
     */
    private static function buildRequestBodyPayloadTypes(array $requestBodies): array
    {
        $requestBodyPayloadTypes = [];

        foreach ($requestBodies as $requestBody) {
            $requestBodyPayloadTypes[$requestBody->getPayloadNormalizedType()] = $requestBody->getPayloadType();
        }

        return array_values($requestBodyPayloadTypes);
    }

    /**
     * @return array<?string>
     *
     * @throws Exception
     */
    private static function buildResponseContentTypes(
        Operation $operation,
        ?Components $components,
    ): array {
        $responseContentTypes = [];

        foreach ($operation->responses->responses ?? [] as $code => $response) {
            if ($response instanceof Reference) {
                if ($components === null || !isset($components->responses[$response->getName()])) {
                    throw new Exception('Reference not found in responses components.');
                }
                $response = $components->responses[$response->getName()];
            }
            if (\count($response->content) === 0 && $code >= 200 && $code < 400) {
                $responseContentTypes['Empty'] = null;
            }
            foreach ($response->content as $type => $mediaType) {
                $responseContentTypes[(string) u($type)->camel()->title()] = $type;
            }
        }

        return array_values($responseContentTypes);
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<?Type>           $requestBodyPayloadTypes
     * @param array<?string>         $responseContentTypes
     *
     * @return array<ActionCase>
     *
     * @throws Exception
     */
    private static function buildCases(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        ?Components $components,
        array $parameters,
        array $requestBodyPayloadTypes,
        array $responseContentTypes,
    ): array {
        $cases = [];

        foreach ($requestBodyPayloadTypes as $requestBodyPayloadType) {
            foreach ($responseContentTypes as $responseContentType) {
                if ($responseContentType !== null) {
                    $cases[] = ActionCase::build(
                        $bundleNamespace,
                        $aggregateName,
                        $actionClassName,
                        $requestBodyPayloadType,
                        $responseContentType,
                        $parameters,
                        $operation,
                        $components,
                    );
                }
            }
        }

        return $cases;
    }

    /**
     * @return mixed[]
     */
    // todo pass controllerClassName in constructor
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

        $actionMethod->setReturnType('Response')
            ->addStmt(new Expression(new Assign($f->var('errors'), $f->val([]))))
        ;

        foreach ($this->getParameters(['path']) as $parameter) {
            $actionMethod->addStmts($parameter->getPathSanitizationStmts());
        }

        foreach ($this->getParameters(['query', 'header', 'cookie']) as $parameter) {
            $actionMethod->addStmts($parameter->getNonPathSanitizationStmts());
        }

        if (\count($this->requestBodies) > 0) {
            $actionMethod->addStmt(new Expression(new Assign($f->var('requestBodyPayload'), $f->val(null))))
                ->addStmt(new Switch_(new Assign($f->var('requestBodyPayloadContentType'), $f->methodCall($f->propertyFetch($f->var('request'), 'headers'), 'get', [$f->val('content-type'), $f->val('')])), array_merge(array_map(
                    static fn (ActionRequestBody $actionRequestBody): Case_ => $actionRequestBody->getCase(),
                    $this->requestBodies,
                ),
                    [new Case_(null, [
                        new Return_($f->new('JsonResponse', [
                            new Array_([
                                new ArrayItem($f->val('unsupported_request_type'), $f->val('code')),
                                new ArrayItem(new InterpolatedString([new InterpolatedStringPart('The value \''), $f->var('requestBodyPayloadContentType'), new InterpolatedStringPart('\' received in content-type header is not a supported format.')]), $f->val('message')),
                            ]),
                            $f->classConstFetch('Response', 'HTTP_UNSUPPORTED_MEDIA_TYPE'),
                        ])),
                    ])]
                )))
            ;
        }

        $actionMethod->addStmt(new If_(new Greater($f->funcCall('\count', [$f->var('errors')]), $f->val(0)), ['stmts' => [
            new Return_($f->new('JsonResponse', [
                new Array_([
                    new ArrayItem($f->val('validation_failed'), $f->val('code')),
                    new ArrayItem($f->val('Validation has failed.'), $f->val('message')),
                    new ArrayItem($f->var('errors'), $f->val('errors')),
                ]),
                $f->classConstFetch('Response', 'HTTP_BAD_REQUEST'),
            ])),
        ]]))
            ->addStmt(new Expression(new Assign($f->var('responsePayloadContentType'), $f->methodCall($f->propertyFetch($f->var('request'), 'headers'), 'get', [$f->val('accept'), $f->val('application/json')]))))
            ->addStmt(new If_($f->funcCall('str_contains', [$f->var('responsePayloadContentType'), $f->val('*/*')]), ['stmts' => [
                new Expression(new Assign($f->var('responsePayloadContentType'), $f->val('application/json'))),
            ]]))
            ->addStmt(new Switch_($f->val(true), array_merge(
                array_map(static fn (ActionCase $case) => $case->getCase(), $this->cases),
                [new Case_(null, [
                    new Return_($f->new('JsonResponse', [
                        new Array_([
                            new ArrayItem($f->val('unsupported_response_type'), $f->val('code')),
                            new ArrayItem(new InterpolatedString([new InterpolatedStringPart('The value \''), $f->var('responsePayloadContentType'), new InterpolatedStringPart('\' received in accept header is not a supported format.')]), $f->val('message')),
                        ]),
                        $f->classConstFetch('Response', 'HTTP_UNSUPPORTED_MEDIA_TYPE'),
                    ])),
                ])],
            )))
            ->addStmt(new Switch_($f->classConstFetch($f->var('response'), 'CONTENT_TYPE'), array_merge(
                array_map(
                    static fn (?string $responseContentType): Case_ => new Case_($f->val($responseContentType), [
                        $responseContentType === null ?
                            new Return_($f->new('Response', [$f->val(''), $f->classConstFetch($f->var('response'), 'CODE'), $f->methodCall($f->var('response'), 'getHeaders')])) :
                            new Return_($f->new('JsonResponse', [$f->propertyFetch($f->var('response'), 'payload'), $f->classConstFetch($f->var('response'), 'CODE'), $f->methodCall($f->var('response'), 'getHeaders')])),
                    ]),
                    $this->responseContentTypes,
                ),
                [new Case_(null, [new Expression(new Throw_($f->new('\RuntimeException')))])],
            )))
        ;

        return $actionMethod->getNode();
    }
}
