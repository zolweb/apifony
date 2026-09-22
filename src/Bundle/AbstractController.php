<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\PrettyPrinter\Standard;

class AbstractController implements File
{
    private const TYPE_ERROR_MESSAGES = [
        'string' => 'must be a string.',
        'int' => 'must be an integer.',
        'float' => 'must be a numeric.',
        'bool' => 'must be a boolean.',
    ];

    public function __construct(
        private readonly string $bundleNamespace,
    ) {
    }

    public function getFolder(): string
    {
        return 'src/Api';
    }

    public function getName(): string
    {
        return 'AbstractController.php';
    }

    /**
     * Builds the "Parameter '<subject>' in '<in>' <text>" message of a denormalization failure.
     */
    private static function getMessageAst(string $subjectVariable, string $text): Encapsed
    {
        $f = new BuilderFactory();

        return new Encapsed([
            new EncapsedStringPart('Parameter \''),
            $f->var($subjectVariable),
            new EncapsedStringPart('\' in \''),
            $f->var('in'),
            new EncapsedStringPart("' {$text}"),
        ]);
    }

    /**
     * Statements turning the string $value into a value of the given built in PHP type, reporting
     * failures against $subjectVariable.
     *
     * @return list<Stmt>
     */
    private static function getCoercionStmts(string $type, string $subjectVariable): array
    {
        $f = new BuilderFactory();
        $error = new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst($subjectVariable, self::TYPE_ERROR_MESSAGES[$type])])));

        return match ($type) {
            'string' => [
                new Return_($f->var('value')),
            ],
            'int' => [
                new Expression(new Assign($f->var('absValue'), $f->var('value'))),
                new If_($f->funcCall('str_starts_with', [$f->var('value'), '-']), ['stmts' => [
                    new Expression(new Assign($f->var('absValue'), $f->funcCall('substr', [$f->var('value'), 1]))),
                ]]),
                new If_(new BooleanNot($f->funcCall('ctype_digit', [$f->var('absValue')])), ['stmts' => [$error]]),
                new Return_(new Int_($f->var('value'))),
            ],
            'float' => [
                new Expression(new Assign($f->var('absValue'), $f->var('value'))),
                new If_($f->funcCall('str_starts_with', [$f->var('value'), '-']), ['stmts' => [
                    new Expression(new Assign($f->var('absValue'), $f->funcCall('substr', [$f->var('value'), 1]))),
                ]]),
                new If_(new BooleanNot($f->funcCall('is_numeric', [$f->var('absValue')])), ['stmts' => [$error]]),
                new Return_(new Double($f->var('value'), ['kind' => Double::KIND_FLOAT])),
            ],
            'bool' => [
                new If_(new BooleanNot($f->funcCall('\in_array', [$f->var('value'), new Array_([new ArrayItem($f->val('true')), new ArrayItem($f->val('false'))], ['kind' => Array_::KIND_SHORT]), $f->val(true)])), ['stmts' => [$error]]),
                new Return_(new ArrayDimFetch(new Array_([new ArrayItem($f->val(true), $f->val('true')), new ArrayItem($f->val(false), $f->val('false'))], ['kind' => Array_::KIND_SHORT]), $f->var('value'))),
            ],
            default => throw new \RuntimeException(),
        };
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
            ->addParam($f->param('deserializer')->setType('DeserializerInterface')->makeProtected()->makeReadonly())
            ->addParam($f->param('validator')->setType('ValidatorInterface')->makeProtected()->makeReadonly())
        ;

        $validateParameter = $f->method('validateParameter')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraints')->setType('array'))
            ->setReturnType('void')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * @param list<Constraint> $constraints
                     *
                     * @throws ParameterValidationException
                     */
                    COMMENT
            )
            ->addStmt(new Assign($f->var('violations'), $f->methodCall($f->propertyFetch($f->var('this'), 'validator'), 'validate', [$f->var('value'), $f->var('constraints')])))
            ->addStmt(new If_(new Greater($f->funcCall('\count', [$f->var('violations')]), $f->val(0)), ['stmts' => [
                new Expression(new Throw_($f->new('ParameterValidationException', [
                    $f->funcCall('array_map', [
                        new ArrowFunction(['static' => true, 'params' => [$f->param('violation')->setType('ConstraintViolationInterface')->getNode()], 'expr' => new Ternary(
                            new Identical($f->methodCall($f->var('violation'), 'getPropertyPath'), $f->val('')),
                            new String_($f->methodCall($f->var('violation'), 'getMessage')),
                            new Encapsed([
                                $f->methodCall($f->var('violation'), 'getPropertyPath'),
                                new EncapsedStringPart(': '),
                                $f->methodCall($f->var('violation'), 'getMessage'),
                            ]),
                        )]),
                        $f->funcCall('iterator_to_array', [$f->var('violations')]),
                    ]),
                ]))),
            ]]))
        ;

        $validateRequestBody = $f->method('validateRequestBody')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraints')->setType('array'))
            ->setReturnType('void')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * @param list<Constraint> $constraints
                     *
                     * @throws RequestBodyValidationException
                     */
                    COMMENT
            )
            ->addStmt(new Assign($f->var('violations'), $f->methodCall($f->propertyFetch($f->var('this'), 'validator'), 'validate', [$f->var('value'), $f->var('constraints')])))
            ->addStmt(new If_(new Greater($f->funcCall('\count', [$f->var('violations')]), $f->val(0)), ['stmts' => [
                new Expression(new Assign($f->var('errors'), $f->val(new Array_([], ['kind' => Array_::KIND_SHORT])))),
                new Foreach_($f->var('violations'), $f->var('violation'), ['stmts' => [
                    new Expression(new Assign($f->var('path'), $f->methodCall($f->var('violation'), 'getPropertyPath'))),
                    new If_(new BooleanNot($f->funcCall('isset', [new ArrayDimFetch($f->var('errors'), $f->var('path'))])), ['stmts' => [
                        new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->var('path')), $f->val(new Array_([], ['kind' => Array_::KIND_SHORT])))),
                    ]]),
                    new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->var('path'))), new String_($f->methodCall($f->var('violation'), 'getMessage')))),
                ]]),
                new Expression(new Throw_($f->new('RequestBodyValidationException', [$f->var('errors')]))),
            ]]))
        ;

        $class = $f->class('AbstractController')
            ->makeAbstract()
            ->addStmt($constructor)
        ;

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            foreach ([false, true] as $nullable) {
                $getParameterMethod = $f->method(\sprintf('get%s%sParameter', ucfirst($type), $nullable ? 'OrNull' : ''))
                    ->makePublic()
                    ->addParam($f->param('request')->setType('Request'))
                    ->addParam($f->param('name')->setType('string'))
                    ->addParam($f->param('in')->setType('string'))
                    ->addParam($f->param('required')->setType('bool'))
                    ->addParam($f->param('default')->setType("?{$type}")->setDefault(null))
                    ->setReturnType(\sprintf("%s{$type}", $nullable ? '?' : ''))
                    ->setDocComment(
                        <<<'COMMENT'
                            /**
                             * @throws DenormalizationException
                             */
                            COMMENT
                    )
                    ->addStmt(new Expression(new Assign($f->var('isset'), $f->methodCall($f->var('this'), 'hasParameter', [$f->var('request'), $f->var('name'), $f->var('in')]))))
                    ->addStmt(new Expression(new Assign($f->var('value'), $f->methodCall($f->var('this'), 'getRawParameter', [$f->var('request'), $f->var('name'), $f->var('in')]))))
                    ->addStmt(new If_(new BooleanNot($f->var('isset')), ['stmts' => array_merge(
                        [new If_($f->var('required'), ['stmts' => [
                            new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('name', 'is required.')]))),
                        ]])],
                        $nullable
                            ? []
                            : [new If_(new Identical($f->var('default'), $f->val(null)), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('name', 'must not be null.')]))),
                            ]])],
                        [new Return_($f->var('default'))],
                    )]))
                    ->addStmt(new If_(new Identical($f->var('value'), $f->val(null)), ['stmts' => [
                        $nullable
                            ? new Return_($f->val(null))
                            : new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('name', 'must not be null.')]))),
                    ]]))
                    ->addStmt(new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [
                        new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('name', self::TYPE_ERROR_MESSAGES[$type])]))),
                    ]]))
                    ->addStmts(self::getCoercionStmts($type, 'name'))
                ;

                $class->addStmt($getParameterMethod);
            }
        }

        $bagAccess = static fn (string $property, string $method, array $args = []): Expr => $f->methodCall($f->propertyFetch($f->var('request'), $property), $method, $args);
        $inputBagValue = static fn (string $property): Expr => new Coalesce(new ArrayDimFetch($f->methodCall($f->propertyFetch($f->var('request'), $property), 'all'), $f->var('name')), $f->val(null));

        $class->addStmt(
            $f->method('hasParameter')
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'))
                ->addParam($f->param('name')->setType('string'))
                ->addParam($f->param('in')->setType('string'))
                ->setReturnType('bool')
                ->addStmt(new Return_(new Match_($f->var('in'), [
                    new MatchArm([$f->val('query')], $f->funcCall('\array_key_exists', [$f->var('name'), $bagAccess('query', 'all')])),
                    new MatchArm([$f->val('header')], $bagAccess('headers', 'has', [$f->var('name')])),
                    new MatchArm([$f->val('cookie')], $f->funcCall('\array_key_exists', [$f->var('name'), $bagAccess('cookies', 'all')])),
                    new MatchArm(null, new Throw_($f->new('\RuntimeException', [$f->val('Invalid parameter location.')]))),
                ])))
        );

        $class->addStmt(
            $f->method('getRawParameter')
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'))
                ->addParam($f->param('name')->setType('string'))
                ->addParam($f->param('in')->setType('string'))
                ->setReturnType('mixed')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * Reads a parameter without any type constraint. Query and cookie values are read through the
                         * whole bag, as InputBag::get() rejects non scalar values with a BadRequestException that would
                         * escape the validation_failed envelope.
                         */
                        COMMENT
                )
                ->addStmt(new Return_(new Match_($f->var('in'), [
                    new MatchArm([$f->val('query')], $inputBagValue('query')),
                    new MatchArm([$f->val('header')], $bagAccess('headers', 'get', [$f->var('name')])),
                    new MatchArm([$f->val('cookie')], $inputBagValue('cookies')),
                    new MatchArm(null, new Throw_($f->new('\RuntimeException', [$f->val('Invalid parameter location.')]))),
                ])))
        );

        $class->addStmt(
            $f->method('denormalizeListParameter')
                ->makePublic()
                ->addParam($f->param('value')->setType('mixed'))
                ->addParam($f->param('path')->setType('string'))
                ->addParam($f->param('in')->setType('string'))
                ->setReturnType('array')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * Query strings can only express a list with empty brackets, as in 'tags[]=a&tags[]=b'.
                         * Anything that would need its keys to be rewritten is rejected rather than reindexed.
                         *
                         * @return list<mixed>
                         *
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new If_(new BooleanNot($f->funcCall('\is_array', [$f->var('value')])), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('path', 'must be an array.')]))),
                ]]))
                ->addStmt(new If_(new BooleanNot($f->funcCall('array_is_list', [$f->var('value')])), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('path', 'must be a list.')]))),
                ]]))
                ->addStmt(new Return_($f->var('value')))
        );

        $class->addStmt(
            $f->method('denormalizeMapParameter')
                ->makePublic()
                ->addParam($f->param('value')->setType('mixed'))
                ->addParam($f->param('path')->setType('string'))
                ->addParam($f->param('in')->setType('string'))
                ->setReturnType('array')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * @return array<string, mixed>
                         *
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new If_(new BooleanNot($f->funcCall('\is_array', [$f->var('value')])), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('path', 'must be an object.')]))),
                ]]))
                ->addStmt(new Expression(new Assign($f->var('values'), new Array_([], ['kind' => Array_::KIND_SHORT]))))
                ->addStmt(new Foreach_($f->var('value'), $f->var('item'), ['keyVar' => $f->var('key'), 'stmts' => [
                    new Expression(new Assign(new ArrayDimFetch($f->var('values'), new String_($f->var('key'))), $f->var('item'))),
                ]]))
                ->addStmt(new Return_($f->var('values')))
        );

        $class->addStmt(
            $f->method('getRequiredParameterProperty')
                ->makePublic()
                ->addParam($f->param('values')->setType('array'))
                ->addParam($f->param('key')->setType('string'))
                ->addParam($f->param('path')->setType('string'))
                ->addParam($f->param('in')->setType('string'))
                ->setReturnType('mixed')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * @param array<string, mixed> $values
                         *
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new If_(new BooleanNot($f->funcCall('\array_key_exists', [$f->var('key'), $f->var('values')])), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('path', 'is required.')]))),
                ]]))
                ->addStmt(new Return_(new ArrayDimFetch($f->var('values'), $f->var('key'))))
        );

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            $class->addStmt(
                $f->method(\sprintf('denormalize%sParameter', ucfirst($type)))
                    ->makePublic()
                    ->addParam($f->param('value')->setType('mixed'))
                    ->addParam($f->param('path')->setType('string'))
                    ->addParam($f->param('in')->setType('string'))
                    ->setReturnType($type)
                    ->setDocComment(
                        <<<'COMMENT'
                            /**
                             * @throws DenormalizationException
                             */
                            COMMENT
                    )
                    ->addStmt(new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [
                        new Expression(new Throw_($f->new('DenormalizationException', [self::getMessageAst('path', self::TYPE_ERROR_MESSAGES[$type])]))),
                    ]]))
                    ->addStmts(self::getCoercionStmts($type, 'path'))
            );
        }

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            foreach ([false, true] as $nullable) {
                $getParameterMethod = $f->method(\sprintf('get%s%sRequestBody', ucfirst($type), $nullable ? 'OrNull' : ''))
                    ->makePublic()
                    ->addParam($f->param('request')->setType('Request'))
                    ->addParam($f->param('default')->setType("?{$type}")->setDefault(null))
                    ->setReturnType(\sprintf("%s{$type}", $nullable ? '?' : ''))
                    ->setDocComment(
                        <<<'COMMENT'
                            /**
                             * @throws DenormalizationException
                             */
                            COMMENT
                    )
                    ->addStmt(new Expression(new Assign($f->var('value'), $f->methodCall($f->var('request'), 'getContent'))))
                    ->addStmt(new If_(new Identical($f->var('value'), $f->val('')), ['stmts' => array_merge(
                        $nullable
                            ? []
                            : [new If_(new Identical($f->var('default'), $f->val(null)), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must not be null.')]))),
                            ]])],
                        [new Return_($f->var('default'))],
                    )]))
                    ->addStmt(new Expression(new Assign($f->var('value'), $f->funcCall('json_decode', [$f->var('value'), $f->val(true)]))))
                    ->addStmts(
                        $nullable && $type !== 'string'
                            ? [new If_(new Identical($f->var('value'), $f->val(null)), ['stmts' => [
                                new Return_($f->val(null)),
                            ]])]
                            : [],
                    )
                    ->addStmts(match ($type) {
                        'string' => [
                            new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must be a string.')]))),
                            ]]),
                            new Return_($f->var('value')),
                        ],
                        'int' => [
                            new If_(new BooleanNot($f->funcCall('\is_int', [$f->var('value')])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must be an integer.')]))),
                            ]]),
                            new Return_($f->var('value')),
                        ],
                        'float' => [
                            new If_(new BooleanAnd(new BooleanNot($f->funcCall('\is_int', [$f->var('value')])), new BooleanNot($f->funcCall('\is_float', [$f->var('value')]))), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must be a numeric.')]))),
                            ]]),
                            new Return_(new Double($f->var('value'), ['kind' => Double::KIND_FLOAT])),
                        ],
                        'bool' => [
                            new If_(new BooleanNot($f->funcCall('\is_bool', [$f->var('value')])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must be a boolean.')]))),
                            ]]),
                            new Return_($f->var('value')),
                        ],
                    })
                ;

                $class->addStmt($getParameterMethod);
            }
        }

        foreach ([false, true] as $nullable) {
            $questionMark = $nullable ? '?' : '';
            $getParameterMethod = $f->method(\sprintf('getObject%sRequestBody', $nullable ? 'OrNull' : ''))
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'))
                ->addParam($f->param('class')->setType('string'))
                ->addParam($f->param('default')->setType('?object')->setDefault(null))
                ->setReturnType(\sprintf('%sobject', $nullable ? '?' : ''))
                ->setDocComment(
                    <<<COMMENT
                        /**
                         * @template T of object
                         *
                         * @param class-string<T> \$class
                         * @param ?T \$default
                         *
                         * @return {$questionMark}T
                         *
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new Expression(new Assign($f->var('value'), $f->methodCall($f->var('request'), 'getContent'))))
                ->addStmt(new If_(new Identical($f->var('value'), $f->val('')), ['stmts' => array_merge(
                    $nullable
                        ? []
                        : [new If_(new Identical($f->var('default'), $f->val(null)), ['stmts' => [
                            new Expression(new Throw_($f->new('DenormalizationException', [$f->val('Request body must not be null.')]))),
                        ]])],
                    [new Return_($f->var('default'))],
                )]))
                ->addStmts(
                    $nullable
                        ? [new If_(new Identical($f->var('value'), $f->val('null')), ['stmts' => [
                            new Return_($f->val(null)),
                        ]])]
                        : [],
                )
                ->addStmt(
                    new TryCatch(
                        [
                            new Return_($f->methodCall($f->propertyFetch($f->var('this'), 'deserializer'), 'deserialize', [$f->var('value'), $f->var('class')])),
                        ],
                        [
                            new Catch_([new Name('ExceptionInterface'), new Name('\TypeError')], $f->var('e'), [
                                new Expression(new Throw_($f->new('DenormalizationException', [new Encapsed([new EncapsedStringPart('Request body could not be deserialized: '), $f->methodCall($f->var('e'), 'getMessage')])]))),
                            ]),
                        ]
                    ),
                )
            ;

            $class->addStmt($getParameterMethod);
        }

        $class->addStmt($validateParameter)
            ->addStmt($validateRequestBody)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api")
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\Serializer\Exception\ExceptionInterface'))
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintViolationInterface'))
            ->addStmt($f->use('Symfony\Component\Validator\Validator\ValidatorInterface'))
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
