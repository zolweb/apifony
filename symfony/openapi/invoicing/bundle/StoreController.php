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

class StoreController extends AbstractController
{
    #[Route(
        path: '/store/inventory',
        methods: ['get'],
        priority: 0,
    )]
    public function getInventory(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        StoreHandlerInterface $handler,
    ): Response {
        $errors = [];
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

    #[Route(
        path: '/store/order',
        methods: ['post'],
        priority: 0,
    )]
    public function placeOrder(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        StoreHandlerInterface $handler,
    ): Response {
        $errors = [];
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'Order', JsonEncoder::FORMAT);
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
                        $handler->handleEmptyPayloadToApplicationJsonContent(
                        ),
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
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
            case $requestBodyPayload instanceOf Order:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->handleOrderPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        ),
                    null =>
                        $handler->handleOrderPayloadToEmptyContent(
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
            case null:
                return new Response('', $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    #[Route(
        path: '/store/order/{orderId}',
        requirements: [
            'orderId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['get'],
        priority: 0,
    )]
    public function getOrderById(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        StoreHandlerInterface $handler,
        int $orderId,
    ): Response {
        $pOrderId = $orderId;
        $errors = [];
        $violations = $validator->validate(
            $pOrderId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['orderId'] = array_map(
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
                        $handler->handleEmptyPayloadToApplicationJsonContent(
                            $pOrderId,
                        ),
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $pOrderId,
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
            case null:
                return new Response('', $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    #[Route(
        path: '/store/order/{orderId}',
        requirements: [
            'orderId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['delete'],
        priority: 0,
    )]
    public function deleteOrder(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        StoreHandlerInterface $handler,
        int $orderId,
    ): Response {
        $pOrderId = $orderId;
        $errors = [];
        $violations = $validator->validate(
            $pOrderId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['orderId'] = array_map(
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
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $pOrderId,
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
            case null:
                return new Response('', $responsePayload::CODE, $responsePayload->getHeaders());
            default:
                throw new RuntimeException();
        }
    }
}
