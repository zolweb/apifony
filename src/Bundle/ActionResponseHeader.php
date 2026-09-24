<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Param;
use Zol\Apifony\Resolved\Header;

class ActionResponseHeader
{
    /**
     * @throws Exception
     */
    public static function build(
        string $name,
        Header $header,
    ): self {
        $ref = $header->schema;
        if ($ref === null) {
            throw new Exception('Header objets without schema attribute are not supported.', $header->path);
        }
        $schema = $ref->getTarget();
        $type = TypeFactory::build('', $schema);
        if ($type instanceof ObjectType) {
            throw new Exception('Headers of object type are not supported.', $schema->path);
        }
        if ($type instanceof ArrayType) {
            throw new Exception('Headers of array type are not supported.', $schema->path);
        }

        return new self($name, $type);
    }

    private function __construct(
        private readonly string $name,
        private readonly Type $type,
    ) {
    }

    public function getArrayItem(): ArrayItem
    {
        $f = new BuilderFactory();

        return new ArrayItem(new String_($f->propertyFetch($f->var('this'), Naming::forMember($this->name))), $f->val($this->name));
    }

    public function getParam(): Param
    {
        $f = new BuilderFactory();

        return $f->param(Naming::forMember($this->name))->setType($this->type->asName())->makePublic()->makeReadonly()->getNode();
    }
}
