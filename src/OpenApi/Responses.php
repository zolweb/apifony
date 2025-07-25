<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Responses
{
    private const CODES = [
        'default',
        100, 101, 102, 103,
        200, 201, 202, 203, 204, 205, 206, 207, 208, 226,
        300, 301, 302, 303, 304, 305, 306, 307, 308,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414,
        415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
        500, 501, 502, 503, 504, 505, 506, 507, 508, 511,
    ];

    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        $responses = [];
        $extensions = [];
        foreach ($data as $key => $elementData) {
            if (\in_array($key, self::CODES, true)) {
                if (!\is_array($elementData)) {
                    throw new Exception('Responses object array elements must be objects.', $path);
                }
                $responsePath = $path;
                $responsePath[] = (string) $key;
                $responses[$key] = isset($elementData['$ref']) ? Reference::build($elementData, $responsePath) : Response::build($elementData, $responsePath);
            } elseif (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $elementData;
            }
        }

        return new self($responses, $extensions, $path);
    }

    /**
     * @param array<string|int, Reference|Response> $responses
     * @param array<string, mixed>                  $extensions
     * @param list<string>                          $path
     */
    private function __construct(
        public readonly array $responses,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
