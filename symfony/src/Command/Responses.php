<?php

namespace App\Command;

class Responses
{
    public readonly string $className;
    /** @var array<Response> */
    public readonly array $responses;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array& $components, array $data): self
    {
        $responses = new self();
        $responses->className = $className;
        $responses->responses = array_map(
            fn (string $code) => Response::build("{$className}{$code}", $code, $components, $data[$code]),
            array_filter(
                array_keys($data),
                static fn (string $code) => in_array($code, [
                    'default',
                    '100', '101', '102', '103',
                    '200', '201', '202', '203', '204', '205', '206', '207', '208', '226',
                    '300', '301', '302', '303', '304', '305', '306', '307', '308',
                    '400', '401', '402', '403', '404', '405', '406', '407', '408', '409', '410', '411', '412', '413', '414',
                    '415', '416', '417', '418', '421', '422', '423', '424', '425', '426', '428', '429', '431', '451',
                    '500', '501', '502', '503', '504', '505', '506', '507', '508', '511',
                ], true),
            ),
        );

        return $responses;
    }

    private function __construct()
    {
    }

    public function addFiles(array& $files): void
    {
        foreach ($this->responses as $response) {
            $response->addFiles($files);
        }
    }
}