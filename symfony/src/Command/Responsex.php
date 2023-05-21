<?php

namespace App\Command;

class Responsex
{
    public readonly Controller $controller;
    public readonly array $responsex;

    /**
     * @param array<Operation> $operations
     */
    public static function build(Operation $operation, Response $response, MediaType $mediaType): self
    {
        return new self();
        $aggregate->responsex = array_merge(
            ...array_map(
                static fn (Operation $operation) => array_merge(
                    ...array_map(
                        static fn (Response $response) => array_map(
                            static fn (MediaType $mediaType) => new Responsex($operation, $response, $mediaType),
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
}