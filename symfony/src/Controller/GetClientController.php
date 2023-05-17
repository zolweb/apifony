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

class GetClientController extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/{param1}/{param2}/{param3}/{param4}/{param5}',
        requirements: [
            'clientId' => '[^:/?#[]@!$&\'()*+,;=]+',
            'param3' => '-?(0|[1-9]\\d*)(\\.\\d+)?([eE][+-]?\\d+)?',
            'param4' => '-?(0|[1-9]\\d*)',
            'param5' => 'true|false',
            'param1' => '[^:/?#[]@!$&\'()*+,;=]+',
            'param2' => 'item',
        ],
        methods: ['get'],
        priority: 1,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        GetClientHandlerInterface $handler,
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
        $qAgrez = floatval($request->query->get('agrez'));
        $hAzef = strval($request->headers->get('azef'));
        $cAzgrzeg = intval($request->cookies->get('azgrzeg', 10));
        $hGegzer = boolval($request->headers->get('gegzer', true));
        $errors = [];
        $violations = $validator->validate(
            $qAgrez,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['agrez'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('agrez')) {
            $errors['query']['agrez'][] = 'Parameter agrez in query is required.';
        }
        $violations = $validator->validate(
            $hAzef,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['azef'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('azef')) {
            $errors['header']['azef'][] = 'Parameter azef in header is required.';
        }
        $violations = $validator->validate(
            $pClientId,
            [
                new Assert\NotNull,
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
                new Assert\NotNull,
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
                new Assert\NotNull,
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
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param5'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $cAzgrzeg,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['azgrzeg'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hGegzer,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['gegzer'] = array_map(
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
                new Assert\NotNull,
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
                new Assert\NotNull,
                new Format,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = json_decode($request->getContent(), true);
                $violations = $validator->validate($requestBodyPayload, [

]);

                break;
            case 'unspecified':
                $requestBodyPayload = null;
                $violations = [];

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$requestBodyPayloadContentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['body'][$violation->getPropertyPath()][] = $violation->getMessage();
            }
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
        $responsePayloadContentType = $request->headers->get('accept', 'unspecified');
        switch (true) {
            case is_null($requestBodyPayload):
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->handleEmptyPayloadToApplicationJsonContent(
                            $qAgrez,
                            $hAzef,
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
                            $cAzgrzeg,
                            $hGegzer,
                            $pParam1,
                            $pParam2,
                        ),
                    default => (object) [
                        'code' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        'content' => [
                            'code' => 'unsupported_response_type',
                            'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                        ],
                    ],
                };

                break;
            case is_int($requestBodyPayload):
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->handleIntegerPayloadToApplicationJsonContent(
                            $qAgrez,
                            $hAzef,
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
                            $cAzgrzeg,
                            $hGegzer,
                            $pParam1,
                            $pParam2,
                            $requestBodyPayload,
                        ),
                    default => (object) [
                        'code' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        'content' => [
                            'code' => 'unsupported_response_type',
                            'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                        ],
                    ],
                };

                break;
            default:
                throw new RuntimeException();
        }
    }
}
