<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\UnionType;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Operation;
use Zol\Ogen\OpenApi\Reference;

use function Symfony\Component\String\u;

class ActionCase
{
    /**
     * @param array<ActionParameter> $parameters
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        ?Type $requestBodyPayloadType,
        ?string $responseContentType,
        array $parameters,
        Operation $operation,
        ?Components $components,
    ): self {
        $responses = [];
        foreach ($operation->responses->responses ?? [] as $code => $response) {
            if ($response instanceof Reference) {
                if ($components === null || !isset($components->responses[$response->getName()])) {
                    throw new Exception('Reference not found in responses components.');
                }
                $response = $components->responses[$response->getName()];
            }
            if (\count($response->content) === 0) {
                $responses[] = ActionResponse::build(
                    $bundleNamespace,
                    $aggregateName,
                    $actionName,
                    (int) $code,
                    $response,
                    null,
                    null,
                    $components,
                );
            }
            foreach ($response->content as $type => $mediaType) {
                if ($responseContentType === $type) {
                    $responses[] = ActionResponse::build(
                        $bundleNamespace,
                        $aggregateName,
                        $actionName,
                        (int) $code,
                        $response,
                        $type,
                        $mediaType->schema,
                        $components,
                    );
                }
            }
        }

        return new self(
            $requestBodyPayloadType,
            $responseContentType,
            \sprintf(
                '%sFrom%sPayloadTo%sContent',
                u($operation->operationId)->camel(),
                $requestBodyPayloadType?->getNormalizedType() ?? 'Empty',
                $responseContentType === null ? 'Empty' : u($responseContentType)->camel()->title(),
            ),
            $parameters,
            $responses,
        );
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionResponse>  $responses
     */
    private function __construct(
        private readonly ?Type $requestBodyPayloadType,
        private readonly ?string $responseContentType,
        private readonly string $name,
        private readonly array $parameters,
        private readonly array $responses,
    ) {
    }

    public function getRequestBodyPayloadTypeName(): Name
    {
        $type = $this->requestBodyPayloadType?->asName();

        if ($type === null) {
            throw new \RuntimeException();
        }

        return $type;
    }

    /**
     * @return array<ActionResponse>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getCase(): Case_
    {
        $f = new BuilderFactory();

        return new Case_(new BooleanAnd($this->requestBodyPayloadType === null ? $f->funcCall('is_null', [$f->var('requestBodyPayload')]) : $this->requestBodyPayloadType->getRequestBodyPayloadTypeCheckingAst(), new Identical($f->var('responsePayloadContentType'), $f->val($this->responseContentType))), [
            new Expression(new Assign($f->var('response'), $f->methodCall($f->propertyFetch($f->var('this'), 'handler'), $this->name, array_merge(
                array_map(static fn (ActionParameter $parameter): Variable => $parameter->asVariable(), $this->parameters),
                $this->requestBodyPayloadType !== null ? [$f->var('requestBodyPayload')] : [],
            )))),
            new Break_(),
        ]);
    }

    public function getHandlerMethod(): ClassMethod
    {
        $f = new BuilderFactory();

        return $f->method($this->name)
            ->makePublic()
            ->addParams(array_merge(
                array_map(
                    static fn (ActionParameter $param): Param => $param->asParam(),
                    $this->parameters,
                ),
                $this->requestBodyPayloadType !== null ? [$f->param('requestBodyPayload')->setType($this->getRequestBodyPayloadTypeName())] : [],
            ))
            ->setReturnType(new UnionType(array_map(
                static fn (ActionResponse $response) => new Name($response->getClassName()),
                $this->responses,
            )))->getNode()
        ;
    }
}
