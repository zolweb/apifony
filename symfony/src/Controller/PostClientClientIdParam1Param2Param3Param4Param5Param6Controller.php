<?php

namespace App\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostClientClientIdParam1Param2Param3Param4Param5Param6Controller extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/{param1}/{param2}/{param3}/{param4}/{param5}',
        requirements: [
            'clientId' => '[^:/?#[]@!$&\\'()*+,;=]+',
            'param3' => '-?(0|[1-9]\\d*)(\\.\\d+)?([eE][+-]?\\d+)?',
            'param4' => '-?(0|[1-9]\\d*)',
            'param5' => 'true|false',
            'param1' => '[^:/?#[]@!$&\\'()*+,;=]+',
            'param2' => 'item',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface $handler,
        string $clientId,
        float $param3,
        int $param4,
        bool $param5,
        string $param1,
        string $param2,
    ): Response {
        $pClientId = $clientId;
        $pParam3 = $param3;
        $pParam4 = $param4;
        $pParam5 = $param5;
        $pParam1 = $param1;
        $pParam2 = $param2;
        $errors = [];
        $violations = $validator->validate(
            $pClientId,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['clientId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam3,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\LessThanOrEqual(value: 2),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam4,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam5,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param5'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam1,
            [
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
                new Format,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam2,
            [
                new Assert\Regex(pattern: 'item'),
                new Assert\Length(min: 1),
                new Assert\Length(max: 10),
                new Assert\Choice(choices: [
                    'item',
                    'item1',
                ]),
                new Format,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }
        switch (true) {
            case is_null($content):
                $responseContent = match ($request->headers->get('accept', 'unspecified')) {
                    'ApplicationJson' =>
                        $handler->handleEmptyApplicationJson(
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
                            $pParam1,
                            $pParam2,
                        ),
                    default =>
                        new class {
                            public readonly string $code;
                            public readonly array $content;
                            public readonly array $headers;
                            public function __construct()
                            {
                                $this->code = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
                                $this->content = [
                                    'code' => 'unsupported_response_type',
                                    'message' => "The value '$contentType' received in accept header is not a supported format.",
                                ];
                                $this->headers = [];
                            }
                        },
                };
            default:
                throw new RuntimeException();
        }
    }
}
