<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;

class Aggregate
{
    private readonly string $folder;
    private readonly string $bundleNamespace;
    private readonly string $name;
    private readonly Handler $handler;
    private readonly Controller $controller;
    private readonly array $responsex;

    /**
     * @param array<Operation> $operations
     */
    public static function build(
        string $bundleNamespace,
        string $name,
        array $operations,
        AbstractController $abstractController,
        Components $components,
    ): self {
        $aggregate = new self();
        $aggregate->handler = Handler::build(
            $bundleNamespace,
            $name,
            $operations,
            $components,
        );
        $aggregate->controller = Controller::build(
            $bundleNamespace,
            $name,
            $operations,
            $components,
            $abstractController,
            $aggregate->handler,
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
        $files[] = $this->handler;
        $files[] = $this->controller;
    }
}