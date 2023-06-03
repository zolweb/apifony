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
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Format as AssertFormat;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F1 as AssertF1;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F2 as AssertF2;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F3 as AssertF3;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F4 as AssertF4;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F5 as AssertF5;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F6 as AssertF6;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F7 as AssertF7;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F9 as AssertF9;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F10 as AssertF10;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F13 as AssertF13;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F12 as AssertF12;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F14 as AssertF14;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F15 as AssertF15;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\F16 as AssertF16;

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
        $hazef = $this->getStringParameter($request, 'azef', 'header', true);
        $qagrez = $this->getFloatParameter($request, 'agrez', 'query', true);
        $cazgrzeg = $this->getIntParameter($request, 'azgrzeg', 'cookie', false, 10);
        $hgegzer = $this->getBoolParameter($request, 'gegzer', 'header', false, true);
        $errors = [];
        $violations = $this->validator->validate(
            $pclientId,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['clientId'] = array_map(
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('azef')) {
            $errors['header']['azef'][] = 'parameter azef in header is required.';
        }
        $violations = $this->validator->validate(
            $qagrez,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['agrez'] = array_map(
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('agrez')) {
            $errors['query']['agrez'][] = 'parameter agrez in query is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        switch ($requestbodypayloadcontenttype = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestbodypayload = null;
                $violations = [];

                break;
            default:
                return new jsonresponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "the value '$requestbodypayloadcontenttype' received in content-type header is not a supported format.",
                    ],
                    response::http_unsupported_media_type,
                );
        }
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
            }
        }
        if (count($errors) > 0) {
            return new jsonresponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'validation has failed.',
                    'errors' => $errors,
                ],
                response::http_bad_request,
            );
        }
        $responsepayloadcontenttype = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsepayloadcontenttype) {
                    case null:
                        $response = $this->handler->GetClientFromEmptyPayloadToContent(
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
                        );

                        break;
                    default:
                        return new jsonresponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "the value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            response::http_unsupported_media_type,
                        );
                }

                break;
            default:
                throw new runtimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }}

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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        switch ($requestbodypayloadcontenttype = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestbodypayload = null;
                $violations = [];

                break;
            default:
                return new jsonresponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "the value '$requestbodypayloadcontenttype' received in content-type header is not a supported format.",
                    ],
                    response::http_unsupported_media_type,
                );
        }
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
            }
        }
        if (count($errors) > 0) {
            return new jsonresponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'validation has failed.',
                    'errors' => $errors,
                ],
                response::http_bad_request,
            );
        }
        $responsepayloadcontenttype = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsepayloadcontenttype) {
                    case null:
                        $response = $this->handler->PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToContent(
                            $pclientId,
                            $pparam3,
                            $pparam4,
                            $pparam5,
                            $pparam1,
                            $pparam2,
                        );

                        break;
                    default:
                        return new jsonresponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "the value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            response::http_unsupported_media_type,
                        );
                }

                break;
            default:
                throw new runtimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }}

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
        $hh1 = $this->getStringParameter($request, 'h1', 'header', true, 'abc');
        $hh2 = $this->getIntParameter($request, 'h2', 'header', true, 1);
        $hh3 = $this->getFloatParameter($request, 'h3', 'header', true, 0.1);
        $hh4 = $this->getBoolParameter($request, 'h4', 'header', true, true);
        $qq1 = $this->getStringParameter($request, 'q1', 'query', true, 'abc');
        $qq2 = $this->getIntParameter($request, 'q2', 'query', true, 1);
        $qq3 = $this->getFloatParameter($request, 'q3', 'query', true, 0.1);
        $qq4 = $this->getBoolParameter($request, 'q4', 'query', true, true);
        $cc1 = $this->getStringParameter($request, 'c1', 'cookie', true, 'abc');
        $cc2 = $this->getIntParameter($request, 'c2', 'cookie', true, 1);
        $cc3 = $this->getFloatParameter($request, 'c3', 'cookie', true, 0.1);
        $cc4 = $this->getBoolParameter($request, 'c4', 'cookie', true, true);
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h1')) {
            $errors['header']['h1'][] = 'parameter h1 in header is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h2')) {
            $errors['header']['h2'][] = 'parameter h2 in header is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h3')) {
            $errors['header']['h3'][] = 'parameter h3 in header is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('h4')) {
            $errors['header']['h4'][] = 'parameter h4 in header is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q1')) {
            $errors['query']['q1'][] = 'parameter q1 in query is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q2')) {
            $errors['query']['q2'][] = 'parameter q2 in query is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q3')) {
            $errors['query']['q3'][] = 'parameter q3 in query is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('q4')) {
            $errors['query']['q4'][] = 'parameter q4 in query is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c1')) {
            $errors['cookie']['c1'][] = 'parameter c1 in cookie is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c2')) {
            $errors['cookie']['c2'][] = 'parameter c2 in cookie is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c3')) {
            $errors['cookie']['c3'][] = 'parameter c3 in cookie is required.';
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
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->cookies->has('c4')) {
            $errors['cookie']['c4'][] = 'parameter c4 in cookie is required.';
        }
        switch ($requestbodypayloadcontenttype = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestbodypayload = null;
                $violations = [];

                break;
            default:
                return new jsonresponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "the value '$requestbodypayloadcontenttype' received in content-type header is not a supported format.",
                    ],
                    response::http_unsupported_media_type,
                );
        }
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
            }
        }
        if (count($errors) > 0) {
            return new jsonresponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'validation has failed.',
                    'errors' => $errors,
                ],
                response::http_bad_request,
            );
        }
        $responsepayloadcontenttype = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsepayloadcontenttype) {
                    case null:
                        $response = $this->handler->PostTestFromEmptyPayloadToContent(
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
                        );

                        break;
                    default:
                        return new jsonresponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "the value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            response::http_unsupported_media_type,
                        );
                }

                break;
            default:
                throw new runtimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }}
}
