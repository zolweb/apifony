<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

use App\Zol\Invoicing\Presentation\Api\Bundle\Api\DenormalizationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\ParameterValidationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\RequestBodyValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\AbstractController;
use App\Zol\Invoicing\Presentation\Api\Bundle\Model\User;

class UserController extends AbstractController
{
    private UserHandler $handler;

    public function setHandler(UserHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function createUser(
        Request $request,
    ): Response {
        $errors = [];

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

                break;
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, User::class);
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
                        $response = $this->handler->CreateUserFromEmptyPayloadToApplicationJsonContent(
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
            case $requestBodyPayload instanceOf User:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->CreateUserFromUserPayloadToApplicationJsonContent(
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

    public function createUsersWithListInput(
        Request $request,
    ): Response {
        $errors = [];

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

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
                    case null:
                        $response = $this->handler->CreateUsersWithListInputFromEmptyPayloadToContent(
                        );

                        break;
                    case 'application/json':
                        $response = $this->handler->CreateUsersWithListInputFromEmptyPayloadToApplicationJsonContent(
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            case 'application/json':
                return new JsonResponse($response->payload, $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function loginUser(
        Request $request,
    ): Response {
        $errors = [];

        try {
            $qUsername = $this->getStringParameter($request, 'username', 'query', false);
            $this->validateParameter(
                $qUsername,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['username'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['username'] = $e->messages;
        }

        try {
            $qPassword = $this->getStringParameter($request, 'password', 'query', false);
            $this->validateParameter(
                $qPassword,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['password'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['password'] = $e->messages;
        }

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

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

        if (!isset(
            $qUsername,
            $qPassword,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->LoginUserFromEmptyPayloadToApplicationJsonContent(
                            $qUsername,
                            $qPassword,
                        );

                        break;
                    case null:
                        $response = $this->handler->LoginUserFromEmptyPayloadToContent(
                            $qUsername,
                            $qPassword,
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function logoutUser(
        Request $request,
    ): Response {
        $errors = [];

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

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
                    case null:
                        $response = $this->handler->LogoutUserFromEmptyPayloadToContent(
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function getUserByName(
        Request $request,
        string $username,
    ): Response {
        $errors = [];

        $pUsername = $username;
        try {
            $this->validateParameter(
                $pUsername,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['username'] = $e->messages;
        }

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

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
                        $response = $this->handler->GetUserByNameFromEmptyPayloadToApplicationJsonContent(
                            $pUsername,
                        );

                        break;
                    case 'application/xml':
                        $response = $this->handler->GetUserByNameFromEmptyPayloadToApplicationXmlContent(
                            $pUsername,
                        );

                        break;
                    case null:
                        $response = $this->handler->GetUserByNameFromEmptyPayloadToContent(
                            $pUsername,
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
            case 'application/xml':
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function updateUser(
        Request $request,
        string $username,
    ): Response {
        $errors = [];

        $pUsername = $username;
        try {
            $this->validateParameter(
                $pUsername,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['username'] = $e->messages;
        }

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

                break;
            case 'application/json':
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, User::class);
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
                    case null:
                        $response = $this->handler->UpdateUserFromEmptyPayloadToContent(
                            $pUsername,
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
            case $requestBodyPayload instanceOf User:
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->UpdateUserFromUserPayloadToContent(
                            $pUsername,
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function deleteUser(
        Request $request,
        string $username,
    ): Response {
        $errors = [];

        $pUsername = $username;
        try {
            $this->validateParameter(
                $pUsername,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['username'] = $e->messages;
        }

        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $requestBodyPayload = null;

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
                    case null:
                        $response = $this->handler->DeleteUserFromEmptyPayloadToContent(
                            $pUsername,
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
