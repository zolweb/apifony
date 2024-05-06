<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Param;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Header;
use Zol\Ogen\OpenApi\Reference;

use function Symfony\Component\String\u;

class ActionResponseHeader
{
    /**
     * @throws Exception
     */
    public static function build(
        string $name,
        Reference|Header $header,
        ?Components $components,
    ): self {
        if ($header instanceof Reference) {
            if ($components === null || !isset($components->headers[$header->getName()])) {
                throw new Exception('Reference not found in headers components.');
            }
            $header = $components->headers[$header->getName()];
        }
        $schema = $header->schema;
        if ($schema === null) {
            throw new Exception('Header objets without schema attribute are not supported.');
        }
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.');
            }
            $schema = $components->schemas[$schema->getName()];
        }
        $type = TypeFactory::build('', $schema, $components);
        if ($type instanceof ObjectType) {
            throw new Exception('Headers of object type are not supported.');
        }
        if ($type instanceof ArrayType) {
            throw new Exception('Headers of array type are not supported.');
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

        return new ArrayItem(new String_($f->propertyFetch($f->var('this'), u($this->name)->camel()->toString())), $f->val($this->name));
    }

    public function getParam(): Param
    {
        $f = new BuilderFactory();

        return $f->param(u($this->name)->camel()->toString())->setType($this->type->asName())->makePublic()->makeReadonly()->getNode();
    }
}
