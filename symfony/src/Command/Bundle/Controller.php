<?php

namespace App\Command\Bundle;

class Controller
{
    public readonly string $folder;
    public readonly string $file;
    public readonly string $namespace;
    public readonly string $name;
    /** @var array<Operation> */
    public readonly array $operations;

    public static function build(
        string $folder,
        string $file,
        string $namespace,
        string $name,
        array $operations,
    ): self {
        $aggregate = new self();
        $aggregate->folder = $folder;
        $aggregate->file = $file;
        $aggregate->namespace = $namespace;
        $aggregate->name = $name;
        $aggregate->operations = $operations;

        return $aggregate;
    }

    private function __construct()
    {
    }

    public function getFile(): File
    {
        return new File(
            $this->folder,
            $this->file,
            'controller.php.twig',
            [
                'namespace' => $this->namespace,
                'className' => $this->name,
                'interfaceName' => 'Lol',
                'operations' => $this->operations,
            ],
        );
    }
}