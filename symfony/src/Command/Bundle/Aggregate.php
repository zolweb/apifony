<?php

namespace App\Command\Bundle;

class Aggregate
{
    public readonly string $folder;
    public readonly string $namespace;
    public readonly string $name;
    public readonly Controller $controller;
    public readonly array $responsex;

    /**
     * @param array<Operation> $operations
     */
    public static function build(
        string $folder,
        string $namespace,
        string $name,
        array $operations,
    ): self {
        $aggregate = new self();
        $aggregate->controller = Controller::build(
            $folder,
            "{$name}Controller.php",
            $namespace,
            "{$name}Controller",
            $operations,
        );

        return $aggregate;

        $aggregate->responsex = array_merge(
            ...array_map(
                static fn (Operation $operation) => array_merge(
                    ...array_map(
                        static fn (Response $response) => array_map(
                            static fn (MediaType $mediaType) => Responsex::build($operation, $response, $mediaType),
                            $response->content,
                        ),
                        $operation->responses->responses,
                    ),
                ),
                $operations,
            ),
        );

        // $aggregate->responsex = array_map(
        //     static fn (MediaType $mediaType) => Responsex::build($mediaType),
        //     array_merge(
        //         ...array_map(
        //             static fn (Response $response) => $response->content,
        //             array_merge(
        //                 ...array_map(
        //                     static fn (Operation $operation) => $operation->responses->responses,
        //                     $operations,
        //                 ),
        //             ),
        //         ),
        //     ),
        // );

        foreach ($this->content as $mediaType) {
            if (!isset($files["{$folder}/{$mediaType->className}.php"])) {
                $files["{$folder}/{$mediaType->className}.php"] = [
                    'folder' => $folder,
                    'name' => "{$mediaType->className}.php",
                    'template' => 'response.php.twig',
                    'params' => [
                        'response' => $this,
                        'mediaType' => $mediaType,
                    ],
                ];
                $mediaType->addFiles($files, $folder);
            }
        }
        if (count($this->content) === 0 && !isset($files["{$folder}/{$this->className}Empty.php"])) {
            $files["{$folder}/{$this->className}Empty.php"] = [
                'folder' => $folder,
                'name' => "{$this->className}Empty.php",
                'template' => 'response.php.twig',
                'params' => [
                    'response' => $this,
                    'mediaType' => null,
                ],
            ];
        }

        return $aggregate;
    }

    private function __construct()
    {
    }

    /**
     * @param array<File> $files
     */
    public function addFiles(array& $files): void
    {
        $files[] = $this->controller->getFile();
    }
}