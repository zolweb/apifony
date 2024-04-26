<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;

class Controller implements File
{
    /**
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        Handler $handler,
        array $usedModelNames,
    ): self {
        # todo maybe import all formats and models and let php-cs-fixer remove unused
        $usedFormatConstraintNames = [];
        foreach ($actions as $action) {
            foreach ($action->getParameters() as $parameter) {
                foreach ($parameter->getConstraints() as $constraint) {
                    foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                        $usedFormatConstraintNames[$constraintName] = true;
                    }
                }
            }
        }

        return new self(
            $handler,
            $actions,
            $bundleNamespace,
            $aggregateName,
            array_keys($usedFormatConstraintNames),
            $usedModelNames,
        );
    }

    /**
     * @param array<Action> $actions
     * @param array<string> $usedFormatConstraintNames
     * @param array<string> $usedModelNames
     */
    private function __construct(
        public readonly Handler $handler,
        public readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedFormatConstraintNames,
        private readonly array $usedModelNames,
    ) {
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * @return array<string>
     */
    public function getUsedFormatConstraintNames(): array
    {
        return $this->usedFormatConstraintNames;
    }

    /**
     * @return array<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Controller";
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->aggregateName}Controller.php";
    }

    public function getTemplate(): string
    {
        return 'controller.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'controller';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $setHandlerMethod = $f->method('setHandler')
            ->addParam($f->param('handler')->setType($this->handler->getClassName()))
            ->setReturnType('void')
            ->makePublic()
            ->addStmt(new Assign($f->propertyFetch($f->var('this'), 'handler'), $f->var('handler')));

        $class = $f->class("{$this->aggregateName}Controller")
            ->extend('AbstractController')
            ->addStmt($f->property('handler')->setType($this->handler->getClassName())->makePrivate())
            ->addStmt($setHandlerMethod);

        foreach ($this->actions as $action) {
            $actionMethod = $f->method($action->getName())
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'));

            foreach ($action->getParameters(['path']) as $parameter) {
                $actionMethod->addParam($f->param($parameter->getRawName())->setType($parameter->getPhpType()));
            }

            $actionMethod->setReturnType('Response')
                ->addStmt(new Expression(new Assign($f->var('errors'), $f->val([]))));

            foreach ($action->getParameters(['path']) as $parameter) {
                $actionMethod->addStmt(new Expression(new Assign($f->var($parameter->getVariableName()), $f->var($parameter->getRawName()))))
                    ->addStmt(new TryCatch([
                        new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                            $f->var($parameter->getVariableName()),
                            array_map(
                                static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                                $parameter->getConstraints(),
                            ),
                        ])),
                    ], [
                        new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), $f->propertyFetch($f->var('e'), 'messages'))),
                        ]),
                    ]));
            }

            foreach ($action->getParameters(['query', 'header', 'cookie']) as $parameter) {
                $actionMethod->addStmt(new Expression(new Assign($f->var($parameter->getVariableName()), $parameter->getInitValueAst())))
                    -> addStmt(new TryCatch([
                        new Expression(new Assign($f->var($parameter->getVariableName()), $f->methodCall($f->var('this'), sprintf('get%s%sParameter', ucfirst($parameter->getPhpType()), $parameter->isNullable() ? 'OrNull' : ''), array_merge([$f->var('request'), $parameter->getRawName(), $parameter->getIn(), $parameter->isRequired()], $parameter->hasDefault() ? [$parameter->getDefault()] : [])))),
                        new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                            $f->var($parameter->getVariableName()),
                            array_map(
                                static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                                $parameter->getConstraints(),
                            ),
                        ])),
                    ], [
                        new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), new Array_([new ArrayItem($f->propertyFetch($f->var('e'), 'messages'))]))),
                        ]),
                        new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), $f->propertyFetch($f->var('e'), 'messages'))),
                        ])
                    ]));
            }

            if (count($action->getRequestBodies()) > 0) {
                $actionMethod->addStmt(new Expression(new Assign($f->var('requestBodyPayload'), $f->val(null))))
                    ->addStmt(new Switch_(new Assign($f->var('requestBodyPayloadContentType'), $f->methodCall($f->propertyFetch($f->var('request'), 'headers'), 'get', [$f->val('content-type'), $f->val('')])), array_merge(array_map(
                            static fn (ActionRequestBody $actionRequestBody): Case_ => new Case_($f->val($actionRequestBody->getMimeType() ?? ''), array_merge(
                                match ($actionRequestBody->getMimeType()) {
                                    'application/json' => [
                                        new TryCatch([
                                            new Expression(new Assign($f->var('requestBodyPayload'), $f->methodCall($f->var('this'), sprintf('get%sJsonRequestBody', ucfirst($actionRequestBody->getPayloadBuiltInPhpType())), array_merge([$f->var('request')], $actionRequestBody->getPayloadBuiltInPhpType() === 'object' ? [$f->classConstFetch($actionRequestBody->getPayloadPhpType(), 'class')] : [])))),
                                            new Expression($f->methodCall($f->var('this'), 'validateRequestBody', [
                                                $f->var('requestBodyPayload'),
                                                array_map(
                                                    static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                                                    $actionRequestBody->getConstraints(),
                                                ),
                                            ])),
                                        ], [
                                            new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                                                new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->val('requestBody')), new Array_([new ArrayItem($f->propertyFetch($f->var('e'), 'messages'))]))),
                                            ]),
                                            new Catch_([new Name('RequestBodyValidationException')], $f->var('e'), [
                                                new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->val('requestBody')), $f->propertyFetch($f->var('e'), 'messages'))),
                                            ])
                                        ]),
                                    ],
                                    default => [],
                                },
                                [new Break_()],
                            )),
                            $action->getRequestBodies(),
                        ),
                        [new Case_(null, [
                            new Return_($f->new('JsonResponse', [
                                new Array_([
                                    new ArrayItem($f->val('unsupported_request_type'), $f->val('code')),
                                    new ArrayItem(new InterpolatedString([new InterpolatedStringPart('The value \''), $f->var('requestBodyPayloadContentType'), new InterpolatedStringPart('\' received in content-type header is not a supported format.')])),
                                ]),
                                $f->classConstFetch('Response', 'HTTP_UNSUPPORTED_MEDIA_TYPE'),
                            ]))
                        ])]
                    )));
            }

            $actionMethod->addStmt(new If_(new Greater($f->funcCall('count', [$f->var('errors')]), $f->val(0)), ['stmts' => [
                new Return_($f->new('JsonResponse', [
                    new Array_([
                        new ArrayItem($f->val('validation_failed'), $f->val('code')),
                        new ArrayItem($f->val('Validation has failed'), $f->val('message')),
                        new ArrayItem($f->var('errors'), $f->val('errors')),
                    ]),
                    $f->classConstFetch('Response', 'HTTP_BAD_REQUEST'),
                ]))
            ]]))
                ->addStmt(new Expression(new Assign($f->var('responsePayloadContentType'), $f->methodCall($f->propertyFetch($f->var('request'), 'headers'), 'get', [$f->val('accept'), $f->val('application/json')]))))
                ->addStmt(new If_($f->funcCall('str_contains', [$f->var('responsePayloadContentType'), $f->val('*/*')]), ['stmts' => [
                    new Expression(new Assign($f->var('responsePayloadContentType'), $f->val('application/json'))),
                ]]))
                ->addStmt(new Switch_($f->val(true), array_merge(
                    array_map(
                        static fn (?Type $requestBodyPayloadType): Case_ => new Case_($requestBodyPayloadType === null ? $f->funcCall('is_null', [$f->var('requestBodyPayload')]) : $requestBodyPayloadType->getRequestBodyPayloadTypeCheckingAst(), [
                            new Switch_($f->var('responsePayloadContentType'), array_merge(
                                array_map(
                                    static fn (?string $responseContentType): Case_ =>
                                        // todo move in Action
                                        new Case_($f->val($action->getCase($requestBodyPayloadType, $responseContentType)->getResponseContentType()), [
                                            new Expression(new Assign($f->var('response'), $f->methodCall($f->propertyFetch($f->var('this'), 'handler'), $action->getCase($requestBodyPayloadType, $responseContentType)->getName(), array_merge(
                                                array_map(static fn (ActionParameter $parameter): Variable => $f->var($parameter->getVariableName()), $action->getCase($requestBodyPayloadType, $responseContentType)->getParameters()),
                                                $action->getCase($requestBodyPayloadType, $responseContentType)->hasRequestBodyPayloadParameter() ? [$f->var('requestBodyPayload')] : [],
                                            )))),
                                            new Break_(),
                                        ]),
                                    array_filter($action->getResponseContentTypes()),
                                ),
                                [new Case_(null, [
                                    new Return_($f->new('JsonResponse', [
                                        new Array_([
                                            new ArrayItem($f->val('unsupported_response_type'), $f->val('code')),
                                            new ArrayItem(new InterpolatedString([new InterpolatedStringPart('The value \''), $f->var('responsePayloadContentType'), new InterpolatedStringPart('\' received in accept header is not a supported format.')]), $f->val('message')),
                                        ]),
                                        $f->classConstFetch('Response', 'HTTP_UNSUPPORTED_MEDIA_TYPE'),
                                    ])),
                                ])],
                            )),
                        ]),
                        $action->getRequestBodyPayloadTypes(),
                    ),
                    [new Case_(null, [new Expression(new Throw_($f->new('\RuntimeException')))])],
                )));

            $class->addStmt($actionMethod);
        }

        $namespace = $f->namespace("{$this->bundleNamespace}\Api\\{$this->aggregateName}")
            ->addStmt($f->use("{$this->bundleNamespace}\Api\DenormalizationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\ParameterValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\RequestBodyValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\AbstractController"))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\JsonResponse'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Response'))
            ->addStmt(new Use_([new UseItem(new Name('Symfony\Component\Validator\Constraints'), 'Assert')]));

        foreach ($this->usedFormatConstraintNames as $usedFormatConstraintName) {
            $namespace->addStmt(new Use_([new UseItem(new Name("{$this->bundleNamespace}\Format\\{$usedFormatConstraintName}"), "Assert{$usedFormatConstraintName}")]));
        }

        foreach ($this->usedModelNames as $usedModelName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\Model\\{$usedModelName}"));
        }

        $namespace->addStmt($class);

        return $namespace->getNode();
    }
}