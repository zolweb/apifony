<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;

class AbstractController implements File
{
    private const FILE = 'src/Api/AbstractController.php';

    private const TYPE_ERROR_MESSAGES = [
        'string' => 'This value should be of type string.',
        'int' => 'This value should be of type integer.',
        'float' => 'This value should be of type number.',
        'bool' => 'This value should be of type boolean.',
    ];

    /**
     * Everything that has a name to settle is settled here rather than while printing: the
     * denormalizer emitted for each model, and the models this file imports. What getContent then
     * does is assemble, so no name is invented at a point where the registry is no longer around
     * to be asked.
     *
     * @param list<Aggregate> $aggregates
     * @param list<Model>     $models
     *
     * @throws Exception
     */
    public static function build(string $bundleNamespace, array $aggregates, array $models, NameRegistry $names): self
    {
        $classFqn = "{$bundleNamespace}\\Api\\AbstractController";

        // One denormalizer per model and per source, emitted on the controller every action
        // extends, so that a model shared by several operations is rendered once for the whole
        // bundle.
        //
        // The models are walked breadth first, one whole generation at a time: every model an
        // action denormalizes directly, then everything those reference, and so on. Taking them
        // depth first would emit the same set of methods in a different order.
        $methods = [];
        $usedModelNames = [];
        foreach ([DenormalizationContext::SOURCE_QUERY, DenormalizationContext::SOURCE_JSON] as $source) {
            $context = new DenormalizationContext($source);

            $batch = [];
            foreach ($aggregates as $aggregate) {
                foreach ($aggregate->getDenormalizationRootModels($source) as $model) {
                    self::claimDenormalizer($names, $classFqn, $model, $source);
                    $batch[$model->getName()] = $model;
                }
            }

            $emittedModelNames = [];
            while (\count($batch) > 0) {
                $next = [];
                foreach ($batch as $modelName => $model) {
                    if (isset($emittedModelNames[$modelName])) {
                        continue;
                    }
                    $emittedModelNames[$modelName] = true;
                    $usedModelNames[$modelName] = true;
                    $methods[] = $model->getModelDenormalizerMethod($context);

                    foreach ($model->getDenormalizationChildModels() as $child) {
                        self::claimDenormalizer($names, $classFqn, $child, $source);
                        $next[$child->getName()] = $child;
                    }
                }
                $batch = $next;
            }
        }

        // Several models may share a class name while living in different namespaces, since inline
        // model names are derived from the operation they belong to. Importing one of them here
        // would silently bind the other's denormalizer to the wrong class.
        $modelNamespaces = [];
        foreach ($models as $model) {
            $modelNamespaces[$model->getClassName()][$model->getNamespace()] = true;
        }

        $imports = [];
        foreach (array_keys($usedModelNames) as $usedModelName) {
            foreach (array_keys($modelNamespaces[$usedModelName] ?? []) as $modelNamespace) {
                $names->claimImport(
                    self::FILE,
                    $usedModelName,
                    Origin::spec('model', "{$modelNamespace}\\{$usedModelName}", ['documentation root']),
                );
                $imports[] = "{$modelNamespace}\\{$usedModelName}";
            }
        }

        return new self($bundleNamespace, $methods, $imports);
    }

    /**
     * @param list<ClassMethod> $denormalizerMethods
     * @param list<string>      $modelImports
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly array $denormalizerMethods,
        private readonly array $modelImports,
    ) {
    }

    /**
     * @throws Exception
     */
    private static function claimDenormalizer(NameRegistry $names, string $classFqn, ObjectType $model, string $source): void
    {
        $names->claimMethod(
            $classFqn,
            DenormalizationContext::getModelMethodName($model->getName(), $source),
            Origin::spec('model', $model->getName(), $model->getSchemaPath()),
        );
    }

    public function getFolder(): string
    {
        return \dirname(self::FILE);
    }

    public function getName(): string
    {
        return basename(self::FILE);
    }

    /**
     * A denormalization failure, carrying where it happened and a machine readable code rather than
     * spelling the location out in the sentence.
     */
    private static function getThrowStmt(string $pathVariable, string $code, string $message): Stmt
    {
        $f = new BuilderFactory();

        return new Expression(new Throw_($f->new('DenormalizationException', [$f->var($pathVariable), $f->val($code), $f->val($message)])));
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
        $error = self::getThrowStmt($subjectVariable, 'invalid_type', self::TYPE_ERROR_MESSAGES[$type]);

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

    /**
     * @throws Exception
     */
    public function getContent(): string
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
            ->addParam($f->param('validator')->setType('ValidatorInterface')->makeProtected()->makeReadonly())
        ;

        $validate = $f->method('validate')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('path')->setType('string'))
            ->addParam($f->param('constraints')->setType('array'))
            ->setReturnType('void')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * @param list<Constraint> $constraints
                     *
                     * @throws ValidationException
                     */
                    COMMENT
            )
            ->addStmt(new Assign($f->var('violations'), $f->methodCall($f->propertyFetch($f->var('this'), 'validator'), 'validate', [$f->var('value'), $f->var('constraints')])))
            ->addStmt(new If_(new Greater($f->funcCall('\count', [$f->var('violations')]), $f->val(0)), ['stmts' => [
                new Expression(new Assign($f->var('errors'), new Array_([], ['kind' => Array_::KIND_SHORT]))),
                new Foreach_($f->var('violations'), $f->var('violation'), ['stmts' => [
                    new Expression(new Assign(new ArrayDimFetch($f->var('errors')), new Array_([
                        new ArrayItem($f->methodCall($f->var('this'), 'appendPath', [$f->var('path'), new String_($f->methodCall($f->var('violation'), 'getPropertyPath'))]), $f->val('path')),
                        new ArrayItem($f->methodCall($f->var('this'), 'getViolationCode', [$f->var('violation')]), $f->val('code')),
                        new ArrayItem(new String_($f->methodCall($f->var('violation'), 'getMessage')), $f->val('message')),
                    ], ['kind' => Array_::KIND_SHORT]))),
                ]]),
                new Expression(new Throw_($f->new('ValidationException', [$f->var('errors')]))),
            ]]))
        ;

        $appendPath = $f->method('appendPath')
            ->makePublic()
            ->addParam($f->param('base')->setType('string'))
            ->addParam($f->param('sub')->setType('string'))
            ->setReturnType('string')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * Joins a value location to one of its sub locations, using the property path syntax the
                     * Symfony validator already produces: a dot before a property, brackets around an index.
                     */
                    COMMENT
            )
            ->addStmt(new If_(new Identical($f->var('sub'), $f->val('')), ['stmts' => [new Return_($f->var('base'))]]))
            ->addStmt(new If_(new Identical($f->var('base'), $f->val('')), ['stmts' => [new Return_($f->var('sub'))]]))
            ->addStmt(new Return_(new Ternary(
                $f->funcCall('str_starts_with', [$f->var('sub'), $f->val('[')]),
                new Encapsed([$f->var('base'), $f->var('sub')]),
                new Encapsed([$f->var('base'), new EncapsedStringPart('.'), $f->var('sub')]),
            )))
        ;

        $constraintCodes = [
            'required' => ['NotNull', 'NotBlank'],
            'invalid_type' => ['Type'],
            'invalid_length' => ['Length'],
            'invalid_enum_value' => ['Choice'],
            'out_of_range' => ['GreaterThan', 'GreaterThanOrEqual', 'LessThan', 'LessThanOrEqual', 'Range'],
            'invalid_multiple' => ['DivisibleBy'],
            'invalid_count' => ['Count'],
            'duplicate_values' => ['Unique'],
            'invalid_pattern' => ['Regex'],
        ];
        $arms = [];
        foreach ($constraintCodes as $code => $constraintNames) {
            $arms[] = new MatchArm(
                array_map(static fn (string $constraintName): Expr => $f->classConstFetch("Assert\\{$constraintName}", 'class'), $constraintNames),
                $f->val($code),
            );
        }
        $arms[] = new MatchArm(null, $f->val('invalid_value'));

        $getViolationCode = $f->method('getViolationCode')
            ->makePublic()
            ->addParam($f->param('violation')->setType('ConstraintViolationInterface'))
            ->setReturnType('string')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * A machine readable code for a violation, so that a client does not have to match on the
                     * English sentence.
                     */
                    COMMENT
            )
            ->addStmt(new Expression(new Assign($f->var('constraint'), new Ternary(
                new Instanceof_($f->var('violation'), new Name('ConstraintViolation')),
                $f->methodCall($f->var('violation'), 'getConstraint'),
                $f->val(null),
            ))))
            ->addStmt(new If_(new Identical($f->var('constraint'), $f->val(null)), ['stmts' => [
                new Return_($f->val('invalid_value')),
            ]]))
            ->addStmt(new If_($f->funcCall('str_starts_with', [
                new ClassConstFetch($f->var('constraint'), 'class'),
                $f->val("{$this->bundleNamespace}\\Format\\"),
            ]), ['stmts' => [
                new Return_($f->val('invalid_format')),
            ]]))
            ->addStmt(new Return_(new Match_(new ClassConstFetch($f->var('constraint'), 'class'), $arms)))
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
                            self::getThrowStmt('name', 'required', 'This value is required.'),
                        ]])],
                        $nullable
                            ? []
                            : [new If_(new Identical($f->var('default'), $f->val(null)), ['stmts' => [
                                self::getThrowStmt('name', 'required', 'This value should not be null.'),
                            ]])],
                        [new Return_($f->var('default'))],
                    )]))
                    ->addStmt(new If_(new Identical($f->var('value'), $f->val(null)), ['stmts' => [
                        $nullable
                            ? new Return_($f->val(null))
                            : self::getThrowStmt('name', 'required', 'This value should not be null.'),
                    ]]))
                    ->addStmt(new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [
                        self::getThrowStmt('name', 'invalid_type', self::TYPE_ERROR_MESSAGES[$type]),
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
                    self::getThrowStmt('path', 'invalid_type', 'This value should be an array.'),
                ]]))
                ->addStmt(new If_(new BooleanNot($f->funcCall('array_is_list', [$f->var('value')])), ['stmts' => [
                    self::getThrowStmt('path', 'invalid_type', 'This value should be a list.'),
                ]]))
                ->addStmt(new Return_($f->var('value')))
        );

        $class->addStmt(
            $f->method('denormalizeMapParameter')
                ->makePublic()
                ->addParam($f->param('value')->setType('mixed'))
                ->addParam($f->param('path')->setType('string'))
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
                    self::getThrowStmt('path', 'invalid_type', 'This value should be an object.'),
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
                    self::getThrowStmt('path', 'required', 'This value is required.'),
                ]]))
                ->addStmt(new Return_(new ArrayDimFetch($f->var('values'), $f->var('key'))))
        );

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            $class->addStmt(
                $f->method(\sprintf('denormalize%sParameter', ucfirst($type)))
                    ->makePublic()
                    ->addParam($f->param('value')->setType('mixed'))
                    ->addParam($f->param('path')->setType('string'))
                    ->setReturnType($type)
                    ->setDocComment(
                        <<<'COMMENT'
                            /**
                             * @throws DenormalizationException
                             */
                            COMMENT
                    )
                    ->addStmt(new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [
                        self::getThrowStmt('path', 'invalid_type', self::TYPE_ERROR_MESSAGES[$type]),
                    ]]))
                    ->addStmts(self::getCoercionStmts($type, 'path'))
            );
        }

        // The JSON family. A request body leaf already carries its type and is only checked, where a
        // query string leaf is always a string and has to be converted.
        $jsonThrow = static fn (string $code, string $message): Stmt => self::getThrowStmt('path', $code, $message);

        $class->addStmt(
            $f->method('getJsonRequestBody')
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'))
                ->setReturnType('mixed')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new Expression(new Assign($f->var('value'), $f->methodCall($f->var('request'), 'getContent'))))
                ->addStmt(new If_(new Identical($f->var('value'), $f->val('')), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [$f->val(''), $f->val('required'), $f->val('This value is required.')]))),
                ]]))
                ->addStmt(new Expression(new Assign($f->var('value'), $f->funcCall('json_decode', [$f->var('value'), $f->val(true)]))))
                ->addStmt(new If_(new NotIdentical($f->funcCall('json_last_error'), new ConstFetch(new Name('\JSON_ERROR_NONE'))), ['stmts' => [
                    new Expression(new Throw_($f->new('DenormalizationException', [$f->val(''), $f->val('invalid_json'), $f->val('The request body is not a valid JSON document.')]))),
                ]]))
                ->addStmt(new Return_($f->var('value')))
        );

        $class->addStmt(
            $f->method('denormalizeListJson')
                ->makePublic()
                ->addParam($f->param('value')->setType('mixed'))
                ->addParam($f->param('path')->setType('string'))
                ->setReturnType('array')
                ->setDocComment(
                    <<<'COMMENT'
                        /**
                         * @return list<mixed>
                         *
                         * @throws DenormalizationException
                         */
                        COMMENT
                )
                ->addStmt(new If_(new BooleanNot($f->funcCall('\is_array', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', 'This value should be an array.')]]))
                ->addStmt(new If_(new BooleanNot($f->funcCall('array_is_list', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', 'This value should be a list.')]]))
                ->addStmt(new Return_($f->var('value')))
        );

        $class->addStmt(
            $f->method('denormalizeMapJson')
                ->makePublic()
                ->addParam($f->param('value')->setType('mixed'))
                ->addParam($f->param('path')->setType('string'))
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
                ->addStmt(new If_(new BooleanNot($f->funcCall('\is_array', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', 'This value should be an object.')]]))
                ->addStmt(new Expression(new Assign($f->var('values'), new Array_([], ['kind' => Array_::KIND_SHORT]))))
                ->addStmt(new Foreach_($f->var('value'), $f->var('item'), ['keyVar' => $f->var('key'), 'stmts' => [
                    new Expression(new Assign(new ArrayDimFetch($f->var('values'), new String_($f->var('key'))), $f->var('item'))),
                ]]))
                ->addStmt(new Return_($f->var('values')))
        );

        $class->addStmt(
            $f->method('getRequiredJsonProperty')
                ->makePublic()
                ->addParam($f->param('values')->setType('array'))
                ->addParam($f->param('key')->setType('string'))
                ->addParam($f->param('path')->setType('string'))
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
                ->addStmt(new If_(new BooleanNot($f->funcCall('\array_key_exists', [$f->var('key'), $f->var('values')])), ['stmts' => [$jsonThrow('required', 'This value is required.')]]))
                ->addStmt(new Return_(new ArrayDimFetch($f->var('values'), $f->var('key'))))
        );

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            $class->addStmt(
                $f->method(\sprintf('denormalize%sJson', ucfirst($type)))
                    ->makePublic()
                    ->addParam($f->param('value')->setType('mixed'))
                    ->addParam($f->param('path')->setType('string'))
                    ->setReturnType($type)
                    ->setDocComment(
                        <<<'COMMENT'
                            /**
                             * @throws DenormalizationException
                             */
                            COMMENT
                    )
                    ->addStmts(match ($type) {
                        'string' => [
                            new If_(new BooleanNot($f->funcCall('\is_string', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', self::TYPE_ERROR_MESSAGES['string'])]]),
                            new Return_($f->var('value')),
                        ],
                        'int' => [
                            new If_(new BooleanNot($f->funcCall('\is_int', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', self::TYPE_ERROR_MESSAGES['int'])]]),
                            new Return_($f->var('value')),
                        ],
                        // A number accepts an int as well as a float, as the scalar readers do.
                        'float' => [
                            new If_(new BooleanAnd(new BooleanNot($f->funcCall('\is_int', [$f->var('value')])), new BooleanNot($f->funcCall('\is_float', [$f->var('value')]))), ['stmts' => [$jsonThrow('invalid_type', self::TYPE_ERROR_MESSAGES['float'])]]),
                            new Return_(new Double($f->var('value'), ['kind' => Double::KIND_FLOAT])),
                        ],
                        'bool' => [
                            new If_(new BooleanNot($f->funcCall('\is_bool', [$f->var('value')])), ['stmts' => [$jsonThrow('invalid_type', self::TYPE_ERROR_MESSAGES['bool'])]]),
                            new Return_($f->var('value')),
                        ],
                    })
            );
        }

        foreach ($this->denormalizerMethods as $denormalizerMethod) {
            $class->addStmt($denormalizerMethod);
        }

        $class->addStmt($validate)
            ->addStmt($appendPath)
            ->addStmt($getViolationCode)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api")
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintViolation'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintViolationInterface'))
            ->addStmt($f->use('Symfony\Component\Validator\Constraints')->as('Assert'))
            ->addStmt($f->use('Symfony\Component\Validator\Validator\ValidatorInterface'))
        ;

        foreach ($this->modelImports as $modelImport) {
            $namespace->addStmt($f->use($modelImport));
        }

        $namespace->addStmt($class);

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
