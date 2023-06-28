<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

use App\Zol\Invoicing\Presentation\Api\Bundle\Api\DenormalizationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\ParameterValidationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\RequestBodyValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
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
use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Test;

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
        try {
            $this->validateParameter(
                $pClientId,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['clientId'] = $e->messages;
        }

        $pParam3 = $param3;
        try {
            $this->validateParameter(
                $pParam3,
                [
                    new Assert\NotNull,
                    new Assert\DivisibleBy(value: 1),
                    new Assert\LessThanOrEqual(value: 2),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param3'] = $e->messages;
        }

        $pParam4 = $param4;
        try {
            $this->validateParameter(
                $pParam4,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param4'] = $e->messages;
        }

        $pParam5 = $param5;
        try {
            $this->validateParameter(
                $pParam5,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param5'] = $e->messages;
        }

        $pParam1 = $param1;
        try {
            $this->validateParameter(
                $pParam1,
                [
                    new Assert\NotNull,
                    new AssertFormat,
                    new Assert\Choice(choices: [
                        'item',
                        'item2',
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param1'] = $e->messages;
        }

        $pParam2 = $param2;
        try {
            $this->validateParameter(
                $pParam2,
                [
                    new Assert\NotNull,
                    new AssertFormat,
                    new Assert\Regex(pattern: '/item/'),
                    new Assert\Length(min: 1),
                    new Assert\Length(max: 10),
                    new Assert\Choice(choices: [
                        'item',
                        'item1',
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param2'] = $e->messages;
        }

        $hAzef = '';
        try {
            $hAzef = $this->getStringParameter($request, 'azef', 'header', true);
            $this->validateParameter(
                $hAzef,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['header']['azef'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['azef'] = $e->messages;
        }

        $qAgrez = .0;
        try {
            $qAgrez = $this->getFloatParameter($request, 'agrez', 'query', true);
            $this->validateParameter(
                $qAgrez,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['agrez'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['agrez'] = $e->messages;
        }

        $cAzgrzeg = 0;
        try {
            $cAzgrzeg = $this->getIntParameter($request, 'azgrzeg', 'cookie', false, 10);
            $this->validateParameter(
                $cAzgrzeg,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['azgrzeg'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['azgrzeg'] = $e->messages;
        }

        $hGegzer = false;
        try {
            $hGegzer = $this->getBoolParameter($request, 'gegzer', 'header', false, true);
            $this->validateParameter(
                $hGegzer,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['header']['gegzer'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['gegzer'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':


                break;
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getIntJsonRequestBody($request);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

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
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getClientFromEmptyPayloadToApplicationJsonContent(
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
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            case is_int($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->getClientFromIntegerPayloadToApplicationJsonContent(
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
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
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
        try {
            $this->validateParameter(
                $pClientId,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['clientId'] = $e->messages;
        }

        $pParam3 = $param3;
        try {
            $this->validateParameter(
                $pParam3,
                [
                    new Assert\NotNull,
                    new Assert\DivisibleBy(value: 1),
                    new Assert\LessThanOrEqual(value: 2),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param3'] = $e->messages;
        }

        $pParam4 = $param4;
        try {
            $this->validateParameter(
                $pParam4,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param4'] = $e->messages;
        }

        $pParam5 = $param5;
        try {
            $this->validateParameter(
                $pParam5,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param5'] = $e->messages;
        }

        $pParam1 = $param1;
        try {
            $this->validateParameter(
                $pParam1,
                [
                    new Assert\NotNull,
                    new AssertFormat,
                    new Assert\Choice(choices: [
                        'item',
                        'item2',
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param1'] = $e->messages;
        }

        $pParam2 = $param2;
        try {
            $this->validateParameter(
                $pParam2,
                [
                    new Assert\NotNull,
                    new AssertFormat,
                    new Assert\Regex(pattern: '/item/'),
                    new Assert\Length(min: 1),
                    new Assert\Length(max: 10),
                    new Assert\Choice(choices: [
                        'item',
                        'item1',
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['param2'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':


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
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postClientClientIdParam1Param2Param3Param4Param5Param6FromEmptyPayloadToApplicationJsonContent(
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
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
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
        try {
            $this->validateParameter(
                $pP1,
                [
                    new Assert\NotNull,
                    new AssertF1,
                    new Assert\Regex(pattern: '/[a-z]{3}/'),
                    new Assert\Length(min: 3),
                    new Assert\Length(max: 3),
                    new Assert\Choice(choices: [
                        'abc',
                        'def',
                        'ghi',
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['p1'] = $e->messages;
        }

        $pP2 = $p2;
        try {
            $this->validateParameter(
                $pP2,
                [
                    new Assert\NotNull,
                    new AssertF2,
                    new Assert\DivisibleBy(value: 1),
                    new Assert\GreaterThanOrEqual(value: 1),
                    new Assert\LessThanOrEqual(value: 3),
                    new Assert\Choice(choices: [
                        1,
                        2,
                        3,
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['p2'] = $e->messages;
        }

        $pP3 = $p3;
        try {
            $this->validateParameter(
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
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['p3'] = $e->messages;
        }

        $pP4 = $p4;
        try {
            $this->validateParameter(
                $pP4,
                [
                    new Assert\NotNull,
                    new AssertF4,
                    new Assert\Choice(choices: [
                        true,
                    ]),
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['p4'] = $e->messages;
        }

        $hH1 = '';
        try {
            $hH1 = $this->getStringParameter($request, 'h1', 'header', true, 'abc');
            $this->validateParameter(
                $hH1,
                [
                    new Assert\NotNull,
                    new AssertF5,
                    new Assert\Regex(pattern: '/[a-z]{3}/'),
                    new Assert\Length(min: 3),
                    new Assert\Length(max: 3),
                    new Assert\Choice(choices: [
                        'abc',
                        'def',
                        'ghi',
                    ]),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h1'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['h1'] = $e->messages;
        }

        $hH2 = 0;
        try {
            $hH2 = $this->getIntParameter($request, 'h2', 'header', true, 1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h2'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['h2'] = $e->messages;
        }

        $hH3 = .0;
        try {
            $hH3 = $this->getFloatParameter($request, 'h3', 'header', true, 0.1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h3'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['h3'] = $e->messages;
        }

        $hH4 = false;
        try {
            $hH4 = $this->getBoolParameter($request, 'h4', 'header', true, true);
            $this->validateParameter(
                $hH4,
                [
                    new Assert\NotNull,
                    new Assert\Choice(choices: [
                        true,
                    ]),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['header']['h4'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['h4'] = $e->messages;
        }

        $qQ1 = '';
        try {
            $qQ1 = $this->getStringParameter($request, 'q1', 'query', true, 'abc');
            $this->validateParameter(
                $qQ1,
                [
                    new Assert\NotNull,
                    new AssertF9,
                    new Assert\Regex(pattern: '/[a-z]{3}/'),
                    new Assert\Length(min: 3),
                    new Assert\Length(max: 3),
                    new Assert\Choice(choices: [
                        'abc',
                        'def',
                        'ghi',
                    ]),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q1'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['q1'] = $e->messages;
        }

        $qQ2 = 0;
        try {
            $qQ2 = $this->getIntParameter($request, 'q2', 'query', true, 1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q2'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['q2'] = $e->messages;
        }

        $qQ3 = .0;
        try {
            $qQ3 = $this->getFloatParameter($request, 'q3', 'query', true, 0.1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q3'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['q3'] = $e->messages;
        }

        $qQ4 = false;
        try {
            $qQ4 = $this->getBoolParameter($request, 'q4', 'query', true, true);
            $this->validateParameter(
                $qQ4,
                [
                    new Assert\NotNull,
                    new AssertF12,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['q4'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['q4'] = $e->messages;
        }

        $cC1 = '';
        try {
            $cC1 = $this->getStringParameter($request, 'c1', 'cookie', true, 'abc');
            $this->validateParameter(
                $cC1,
                [
                    new Assert\NotNull,
                    new AssertF13,
                    new Assert\Regex(pattern: '/[a-z]{3}/'),
                    new Assert\Length(min: 3),
                    new Assert\Length(max: 3),
                    new Assert\Choice(choices: [
                        'abc',
                        'def',
                        'ghi',
                    ]),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c1'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['c1'] = $e->messages;
        }

        $cC2 = 0;
        try {
            $cC2 = $this->getIntParameter($request, 'c2', 'cookie', true, 1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c2'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['c2'] = $e->messages;
        }

        $cC3 = .0;
        try {
            $cC3 = $this->getFloatParameter($request, 'c3', 'cookie', true, 0.1);
            $this->validateParameter(
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
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c3'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['c3'] = $e->messages;
        }

        $cC4 = false;
        try {
            $cC4 = $this->getBoolParameter($request, 'c4', 'cookie', true, true);
            $this->validateParameter(
                $cC4,
                [
                    new Assert\NotNull,
                    new AssertF16,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['cookie']['c4'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['cookie']['c4'] = $e->messages;
        }

        $requestBodyPayload = null;
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':


                break;
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, Test::class);
                    $this->validateRequestBody(
                        $requestBodyPayload,
                        [
                            new Assert\Valid,
                            new Assert\NotNull,
                        ],
                    );
                } catch (DenormalizationException $e) {
                    $errors['requestBody'] = [$e->getMessage()];
                } catch (RequestBodyValidationException $e) {
                    $errors['requestBody'] = $e->messages;
                }

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
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postTestFromEmptyPayloadToApplicationJsonContent(
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
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            case $requestBodyPayload instanceOf Test:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->postTestFromTestPayloadToApplicationJsonContent(
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
                            $requestBodyPayload,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$responsePayloadContentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            default:
                throw new RuntimeException();
        }
        switch ($response::CONTENT_TYPE) {
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
