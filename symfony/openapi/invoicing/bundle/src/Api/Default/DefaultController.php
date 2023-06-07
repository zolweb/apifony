<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

use App\Zol\Invoicing\Presentation\Api\Bundle\Api\DenormalizationException;
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
        $errors = [];

        $pClientId = $clientId;
        $this->validateParameter(
            'clientId',
            'path',
            $pClientId,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam3 = $param3;
        $this->validateParameter(
            'param3',
            'path',
            $pParam3,
            [
                new Assert\NotNull,
                new Assert\DivisibleBy(value: 1),
                new Assert\LessThanOrEqual(value: 2),
            ],
            $errors,
        );

        $pParam4 = $param4;
        $this->validateParameter(
            'param4',
            'path',
            $pParam4,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam5 = $param5;
        $this->validateParameter(
            'param5',
            'path',
            $pParam5,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam1 = $param1;
        $this->validateParameter(
            'param1',
            'path',
            $pParam1,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
            ],
            $errors,
        );

        $pParam2 = $param2;
        $this->validateParameter(
            'param2',
            'path',
            $pParam2,
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
            ],
            $errors,
        );

        try {
            $hAzef = $this->getStringParameter($request, 'azef', 'header', true);
            $this->validateParameter(
                'azef',
                'header',
                $hAzef,
                [
                new Assert\NotNull,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['azef'] = $e->getMessage();
        }

        try {
            $qAgrez = $this->getFloatParameter($request, 'agrez', 'query', true);
            $this->validateParameter(
                'agrez',
                'query',
                $qAgrez,
                [
                new Assert\NotNull,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['query']['agrez'] = $e->getMessage();
        }

        try {
            $cAzgrzeg = $this->getIntParameter($request, 'azgrzeg', 'cookie', false, 10);
            $this->validateParameter(
                'azgrzeg',
                'cookie',
                $cAzgrzeg,
                [
                new Assert\NotNull,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['azgrzeg'] = $e->getMessage();
        }

        try {
            $hGegzer = $this->getBoolParameter($request, 'gegzer', 'header', false, true);
            $this->validateParameter(
                'gegzer',
                'header',
                $hGegzer,
                [
                new Assert\NotNull,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['gegzer'] = $e->getMessage();
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
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
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
        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->GetClientFromEmptyPayloadToContent(
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
                            $hAzef,
                            $qAgrez,
                            $pParam1,
                            $pParam2,
                            $cAzgrzeg,
                            $hGegzer,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
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
        $errors = [];

        $pClientId = $clientId;
        $this->validateParameter(
            'clientId',
            'path',
            $pClientId,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam3 = $param3;
        $this->validateParameter(
            'param3',
            'path',
            $pParam3,
            [
                new Assert\NotNull,
                new Assert\DivisibleBy(value: 1),
                new Assert\LessThanOrEqual(value: 2),
            ],
            $errors,
        );

        $pParam4 = $param4;
        $this->validateParameter(
            'param4',
            'path',
            $pParam4,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam5 = $param5;
        $this->validateParameter(
            'param5',
            'path',
            $pParam5,
            [
                new Assert\NotNull,
            ],
            $errors,
        );

        $pParam1 = $param1;
        $this->validateParameter(
            'param1',
            'path',
            $pParam1,
            [
                new Assert\NotNull,
                new AssertFormat,
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
            ],
            $errors,
        );

        $pParam2 = $param2;
        $this->validateParameter(
            'param2',
            'path',
            $pParam2,
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
            ],
            $errors,
        );

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
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
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
        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->PostClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToContent(
                            $pClientId,
                            $pParam3,
                            $pParam4,
                            $pParam5,
                            $pParam1,
                            $pParam2,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
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
        $errors = [];

        $pP1 = $p1;
        $this->validateParameter(
            'p1',
            'path',
            $pP1,
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
            ],
            $errors,
        );

        $pP2 = $p2;
        $this->validateParameter(
            'p2',
            'path',
            $pP2,
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
            ],
            $errors,
        );

        $pP3 = $p3;
        $this->validateParameter(
            'p3',
            'path',
            $pP3,
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
            ],
            $errors,
        );

        $pP4 = $p4;
        $this->validateParameter(
            'p4',
            'path',
            $pP4,
            [
                new Assert\NotNull,
                new AssertF4,
                new Assert\Choice(choices: [
                    true,
                ]),
            ],
            $errors,
        );

        try {
            $hH1 = $this->getStringParameter($request, 'h1', 'header', true, 'abc');
            $this->validateParameter(
                'h1',
                'header',
                $hH1,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h1'] = $e->getMessage();
        }

        try {
            $hH2 = $this->getIntParameter($request, 'h2', 'header', true, 1);
            $this->validateParameter(
                'h2',
                'header',
                $hH2,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h2'] = $e->getMessage();
        }

        try {
            $hH3 = $this->getFloatParameter($request, 'h3', 'header', true, 0.1);
            $this->validateParameter(
                'h3',
                'header',
                $hH3,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h3'] = $e->getMessage();
        }

        try {
            $hH4 = $this->getBoolParameter($request, 'h4', 'header', true, true);
            $this->validateParameter(
                'h4',
                'header',
                $hH4,
                [
                new Assert\NotNull,
                new Assert\Choice(choices: [
                    true,
                ]),
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h4'] = $e->getMessage();
        }

        try {
            $qQ1 = $this->getStringParameter($request, 'q1', 'query', true, 'abc');
            $this->validateParameter(
                'q1',
                'query',
                $qQ1,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q1'] = $e->getMessage();
        }

        try {
            $qQ2 = $this->getIntParameter($request, 'q2', 'query', true, 1);
            $this->validateParameter(
                'q2',
                'query',
                $qQ2,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q2'] = $e->getMessage();
        }

        try {
            $qQ3 = $this->getFloatParameter($request, 'q3', 'query', true, 0.1);
            $this->validateParameter(
                'q3',
                'query',
                $qQ3,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q3'] = $e->getMessage();
        }

        try {
            $qQ4 = $this->getBoolParameter($request, 'q4', 'query', true, true);
            $this->validateParameter(
                'q4',
                'query',
                $qQ4,
                [
                new Assert\NotNull,
                new AssertF12,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q4'] = $e->getMessage();
        }

        try {
            $cC1 = $this->getStringParameter($request, 'c1', 'cookie', true, 'abc');
            $this->validateParameter(
                'c1',
                'cookie',
                $cC1,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c1'] = $e->getMessage();
        }

        try {
            $cC2 = $this->getIntParameter($request, 'c2', 'cookie', true, 1);
            $this->validateParameter(
                'c2',
                'cookie',
                $cC2,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c2'] = $e->getMessage();
        }

        try {
            $cC3 = $this->getFloatParameter($request, 'c3', 'cookie', true, 0.1);
            $this->validateParameter(
                'c3',
                'cookie',
                $cC3,
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
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c3'] = $e->getMessage();
        }

        try {
            $cC4 = $this->getBoolParameter($request, 'c4', 'cookie', true, true);
            $this->validateParameter(
                'c4',
                'cookie',
                $cC4,
                [
                new Assert\NotNull,
                new AssertF16,
                ],
                $errors,
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c4'] = $e->getMessage();
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
                $errors['body'][$violation->getpropertypath()][] = $violation->getmessage();
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
        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestbodypayload):
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->PostTestFromEmptyPayloadToContent(
                            $pP1,
                            $pP2,
                            $pP3,
                            $pP4,
                            $hH1,
                            $hH2,
                            $hH3,
                            $hH4,
                            $qQ1,
                            $qQ2,
                            $qQ3,
                            $qQ4,
                            $cC1,
                            $cC2,
                            $cC3,
                            $cC4,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsepayloadcontenttype' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
