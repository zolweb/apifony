<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Default;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController
{
    public function getClient(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DefaultHandlerInterface $handler,
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
                        $handler->getClientFromEmptyPayloadToApplicationJsonContent(
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
                        $handler->getClientFromIntegerPayloadToApplicationJsonContent(
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
        switch ($responsePayload::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($responsePayload->payload, $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function postClientClientIdParam1Param2Param3Param4Param5Param6(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DefaultHandlerInterface $handler,
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
                        $handler->postClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
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
            default:
                throw new RuntimeException();
        }
        switch ($responsePayload::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($responsePayload->payload, $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function postTest(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DefaultHandlerInterface $handler,
        string $p1,
        int $p2,
        float $p3,
        bool $p4,
    ): Response {
        $pP1 = $p1;
        $pP2 = $p2;
        $pP3 = $p3;
        $pP4 = $p4;
        $cC1 = strval($request->cookies->get('c1', 'abc'));
        $cC2 = intval($request->cookies->get('c2', 1));
        $cC3 = floatval($request->cookies->get('c3', 0.1));
        $cC4 = boolval($request->cookies->get('c4', true));
        $hH1 = strval($request->headers->get('h1', 'abc'));
        $hH2 = intval($request->headers->get('h2', 1));
        $hH3 = floatval($request->headers->get('h3', 0.1));
        $hH4 = boolval($request->headers->get('h4', true));
        $qQ1 = strval($request->query->get('q1', 'abc'));
        $qQ2 = intval($request->query->get('q2', 1));
        $qQ3 = floatval($request->query->get('q3', 0.1));
        $qQ4 = boolval($request->query->get('q4', true));
        $errors = [];
        $violations = $validator->validate(
            $cC1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new Assert\NotNull,
                new F13,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['c1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c1')) {
            $errors['cookie']['c1'][] = 'Parameter c1 in cookie is required.';
        }
        $violations = $validator->validate(
            $cC2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new Assert\NotNull,
                new F14,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['c2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c2')) {
            $errors['cookie']['c2'][] = 'Parameter c2 in cookie is required.';
        }
        $violations = $validator->validate(
            $cC3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new Assert\NotNull,
                new F15,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['c3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c3')) {
            $errors['cookie']['c3'][] = 'Parameter c3 in cookie is required.';
        }
        $violations = $validator->validate(
            $cC4,
            [
                new Assert\NotNull,
                new F16,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['c4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c4')) {
            $errors['cookie']['c4'][] = 'Parameter c4 in cookie is required.';
        }
        $violations = $validator->validate(
            $hH1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new Assert\NotNull,
                new F5,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['h1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h1')) {
            $errors['header']['h1'][] = 'Parameter h1 in header is required.';
        }
        $violations = $validator->validate(
            $hH2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new Assert\NotNull,
                new F6,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['h2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h2')) {
            $errors['header']['h2'][] = 'Parameter h2 in header is required.';
        }
        $violations = $validator->validate(
            $hH3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new Assert\NotNull,
                new F7,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['h3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h3')) {
            $errors['header']['h3'][] = 'Parameter h3 in header is required.';
        }
        $violations = $validator->validate(
            $hH4,
            [
                new Assert\Choice(choices: [
                    true,
                ]),
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['h4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h4')) {
            $errors['header']['h4'][] = 'Parameter h4 in header is required.';
        }
        $violations = $validator->validate(
            $pP1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new Assert\NotNull,
                new F1,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThan(value: 4),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new Assert\NotNull,
                new F2,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\GreaterThan(value: -1),
                new Assert\Choice(choices: [
                    0.2,
                    0.3,
                    0.1,
                ]),
                new Assert\NotNull,
                new F3,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP4,
            [
                new Assert\Choice(choices: [
                    true,
                ]),
                new Assert\NotNull,
                new F4,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qQ1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new Assert\NotNull,
                new F9,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['q1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q1')) {
            $errors['query']['q1'][] = 'Parameter q1 in query is required.';
        }
        $violations = $validator->validate(
            $qQ2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new Assert\NotNull,
                new F10,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['q2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q2')) {
            $errors['query']['q2'][] = 'Parameter q2 in query is required.';
        }
        $violations = $validator->validate(
            $qQ3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new Assert\NotNull,
                new F13,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['q3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q3')) {
            $errors['query']['q3'][] = 'Parameter q3 in query is required.';
        }
        $violations = $validator->validate(
            $qQ4,
            [
                new Assert\NotNull,
                new F12,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['q4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q4')) {
            $errors['query']['q4'][] = 'Parameter q4 in query is required.';
        }
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'Test', JsonEncoder::FORMAT);
                $violations = $validator->validate($requestBodyPayload);

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
                        $handler->postTestFromEmptyPayloadToApplicationJsonContent(
                            $cC1,
                            $cC2,
                            $cC3,
                            $cC4,
                            $hH1,
                            $hH2,
                            $hH3,
                            $hH4,
                            $pP1,
                            $pP2,
                            $pP3,
                            $pP4,
                            $qQ1,
                            $qQ2,
                            $qQ3,
                            $qQ4,
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
            case $requestBodyPayload instanceOf Test:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->postTestFromTestPayloadToApplicationJsonContent(
                            $cC1,
                            $cC2,
                            $cC3,
                            $cC4,
                            $hH1,
                            $hH2,
                            $hH3,
                            $hH4,
                            $pP1,
                            $pP2,
                            $pP3,
                            $pP4,
                            $qQ1,
                            $qQ2,
                            $qQ3,
                            $qQ4,
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
        switch ($responsePayload::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($responsePayload->payload, $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
