<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\AbstractController;

class DefaultController extends AbstractController
{
    private DefaultHandler $handler;

    public function setHandler(DefaultHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function getClient(
        Request $request,
        string $clientId,
        float $param3,
        int $param4,
        bool $param5,
        string $param1,
        string $param2,
    ): Response {
        $pclientId = $clientId;
        $pparam3 = $param3;
        $pparam4 = $param4;
        $pparam5 = $param5;
        $pparam1 = $param1;
        $pparam2 = $param2;
        $hazef = strval($request->headers->get('azef'));
        $qagrez = floatval($request->query->get('agrez'));
        $cazgrzeg = intval($request->cookies->get('azgrzeg', 10));
        $hgegzer = boolval($request->headers->get('gegzer', true));
        $errors = [];
        $violations = $this->validator->validate(
            $pclientId,
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
        $violations = $this->validator->validate(
            $pparam3,
            [
                new Assert\NotNull,
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
        $violations = $this->validator->validate(
            $pparam4,
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
        $violations = $this->validator->validate(
            $pparam5,
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
        $violations = $this->validator->validate(
            $hazef,
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
        $violations = $this->validator->validate(
            $qagrez,
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
        $violations = $this->validator->validate(
            $pparam1,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $pparam2,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Regex(pattern: 'item'),
                new Assert\Length(min: 1),
                new Assert\Length(max: 10),
                new Assert\Choice(choices: [
                    'item',
                    'item1',
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $cazgrzeg,
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
        $violations = $this->validator->validate(
            $hgegzer,
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
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;
                $violations = [];

                break;
            case 'application/json':
                $requestBodyPayload = json_decode($request->getContent(), true);
                $violations = $this->validator->validate($requestBodyPayload, [
                    new Assert\NotNull
]);

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
                        $this->handler->GetClientFromEmptyPayloadToApplicationJsonContent(
                            $pclientId,
                            $pparam3,
                            $pparam4,
                            $pparam5,
                            $hazef,
                            $qagrez,
                            $pparam1,
                            $pparam2,
                            $cazgrzeg,
                            $hgegzer,
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
                        $this->handler->GetClientFromIntegerPayloadToApplicationJsonContent(
                            $pclientId,
                            $pparam3,
                            $pparam4,
                            $pparam5,
                            $hazef,
                            $qagrez,
                            $pparam1,
                            $pparam2,
                            $cazgrzeg,
                            $hgegzer,
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
        string $clientId,
        float $param3,
        int $param4,
        bool $param5,
        string $param1,
        string $param2,
    ): Response {
        $pclientId = $clientId;
        $pparam3 = $param3;
        $pparam4 = $param4;
        $pparam5 = $param5;
        $pparam1 = $param1;
        $pparam2 = $param2;
        $errors = [];
        $violations = $this->validator->validate(
            $pclientId,
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
        $violations = $this->validator->validate(
            $pparam3,
            [
                new Assert\NotNull,
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
        $violations = $this->validator->validate(
            $pparam4,
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
        $violations = $this->validator->validate(
            $pparam5,
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
        $violations = $this->validator->validate(
            $pparam1,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $pparam2,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Regex(pattern: 'item'),
                new Assert\Length(min: 1),
                new Assert\Length(max: 10),
                new Assert\Choice(choices: [
                    'item',
                    'item1',
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
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
                        $this->handler->PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
                            $pclientId,
                            $pparam3,
                            $pparam4,
                            $pparam5,
                            $pparam1,
                            $pparam2,
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
        string $p1,
        int $p2,
        float $p3,
        bool $p4,
    ): Response {
        $pp1 = $p1;
        $pp2 = $p2;
        $pp3 = $p3;
        $pp4 = $p4;
        $hh1 = strval($request->headers->get('h1', 'abc'));
        $hh2 = intval($request->headers->get('h2', 1));
        $hh3 = floatval($request->headers->get('h3', 0.1));
        $hh4 = boolval($request->headers->get('h4', true));
        $qq1 = strval($request->query->get('q1', 'abc'));
        $qq2 = intval($request->query->get('q2', 1));
        $qq3 = floatval($request->query->get('q3', 0.1));
        $qq4 = boolval($request->query->get('q4', true));
        $cc1 = strval($request->cookies->get('c1', 'abc'));
        $cc2 = intval($request->cookies->get('c2', 1));
        $cc3 = floatval($request->cookies->get('c3', 0.1));
        $cc4 = boolval($request->cookies->get('c4', true));
        $errors = [];
        $violations = $this->validator->validate(
            $pp1,
            [
                new Assert\NotNull,
                new AssertF1,
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $pp2,
            [
                new Assert\NotNull,
                new AssertF2,
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThan(value: 4),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $pp3,
            [
                new Assert\NotNull,
                new AssertF3,
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\GreaterThan(value: -1),
                new Assert\Choice(choices: [
                    0.2,
                    0.3,
                    0.1,
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $pp4,
            [
                new Assert\NotNull,
                new AssertF4,
                new Assert\Choice(choices: [
                    true,
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['p4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $hh1,
            [
                new Assert\NotNull,
                new AssertF5,
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
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
        $violations = $this->validator->validate(
            $hh2,
            [
                new Assert\NotNull,
                new AssertF6,
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
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
        $violations = $this->validator->validate(
            $hh3,
            [
                new Assert\NotNull,
                new AssertF7,
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
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
        $violations = $this->validator->validate(
            $hh4,
            [
                new Assert\NotNull,
                new Assert\Choice(choices: [
                    true,
                ]),
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
        $violations = $this->validator->validate(
            $qq1,
            [
                new Assert\NotNull,
                new AssertF9,
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
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
        $violations = $this->validator->validate(
            $qq2,
            [
                new Assert\NotNull,
                new AssertF10,
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
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
        $violations = $this->validator->validate(
            $qq3,
            [
                new Assert\NotNull,
                new AssertF13,
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
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
        $violations = $this->validator->validate(
            $qq4,
            [
                new Assert\NotNull,
                new AssertF12,
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
        $violations = $this->validator->validate(
            $cc1,
            [
                new Assert\NotNull,
                new AssertF13,
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
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
        $violations = $this->validator->validate(
            $cc2,
            [
                new Assert\NotNull,
                new AssertF14,
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
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
        $violations = $this->validator->validate(
            $cc3,
            [
                new Assert\NotNull,
                new AssertF15,
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
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
        $violations = $this->validator->validate(
            $cc4,
            [
                new Assert\NotNull,
                new AssertF16,
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
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;
                $violations = [];

                break;
            case 'application/json':
                $requestBodyPayload = $this->serializer->deserialize($request->getContent(), 'PostTestApplicationJsonRequestBodyPayload', JsonEncoder::FORMAT);
                $violations = $this->validator->validate($requestBodyPayload);

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
                        $this->handler->PostTestFromEmptyPayloadToApplicationJsonContent(
                            $pp1,
                            $pp2,
                            $pp3,
                            $pp4,
                            $hh1,
                            $hh2,
                            $hh3,
                            $hh4,
                            $qq1,
                            $qq2,
                            $qq3,
                            $qq4,
                            $cc1,
                            $cc2,
                            $cc3,
                            $cc4,
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
            case $requestBodyPayload instanceOf PostTestApplicationJsonRequestBodyPayload:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $this->handler->PostTestFromPostTestApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $pp1,
                            $pp2,
                            $pp3,
                            $pp4,
                            $hh1,
                            $hh2,
                            $hh3,
                            $hh4,
                            $qq1,
                            $qq2,
                            $qq3,
                            $qq4,
                            $cc1,
                            $cc2,
                            $cc3,
                            $cc4,
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
