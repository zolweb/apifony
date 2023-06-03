<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\AbstractController;

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
                        $response = $this->handler->CreateUserFromEmptyPayloadToContent(
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
    }

    public function createUsersWithListInput(
        Request $request,
    ): Response {
        $errors = [];
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
                        $response = $this->handler->CreateUsersWithListInputFromEmptyPayloadToContent(
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
    }

    public function loginUser(
        Request $request,
    ): Response {
        $qusername = $this->getStringParameter($request, 'username', 'query', false);
        $qpassword = $this->getStringParameter($request, 'password', 'query', false);
        $errors = [];
        $violations = $this->validator->validate(
            $qusername,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['username'] = array_map(
                fn (constraintviolationinterface $violation) => $violation->getmessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
            $qpassword,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['password'] = array_map(
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
                        $response = $this->handler->LoginUserFromEmptyPayloadToContent(
                            $qusername,
                            $qpassword,
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
    }

    public function logoutUser(
        Request $request,
    ): Response {
        $errors = [];
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
                        $response = $this->handler->LogoutUserFromEmptyPayloadToContent(
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
    }

    public function getUserByName(
        Request $request,
        string $username,
    ): Response {
        $pusername = $username;
        $errors = [];
        $violations = $this->validator->validate(
            $pusername,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['username'] = array_map(
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
                        $response = $this->handler->GetUserByNameFromEmptyPayloadToContent(
                            $pusername,
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
    }

    public function updateUser(
        Request $request,
        string $username,
    ): Response {
        $pusername = $username;
        $errors = [];
        $violations = $this->validator->validate(
            $pusername,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['username'] = array_map(
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
                        $response = $this->handler->UpdateUserFromEmptyPayloadToContent(
                            $pusername,
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
    }

    public function deleteUser(
        Request $request,
        string $username,
    ): Response {
        $pusername = $username;
        $errors = [];
        $violations = $this->validator->validate(
            $pusername,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['username'] = array_map(
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
                        $response = $this->handler->DeleteUserFromEmptyPayloadToContent(
                            $pusername,
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
    }
}
