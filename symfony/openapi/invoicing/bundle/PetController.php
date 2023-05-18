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

class PetController extends AbstractController
{
    #[Route(
        path: '/pet',
        methods: ['put'],
        priority: 0,
    )]
    public function updatePet(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
    ): Response {
        $errors = [];
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'UpdatePetApplicationJsonMediaTypeSchema', JsonEncoder::FORMAT);
                $violations = $validator->validate($requestBodyPayload);

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
            case $requestBodyPayload instanceOf UpdatePetApplicationJsonMediaTypeSchema:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->handleUpdatePetApplicationJsonMediaTypeSchemaPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        ),
                    null =>
                        $handler->handleUpdatePetApplicationJsonMediaTypeSchemaPayloadToEmptyContent(
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
        path: '/pet',
        methods: ['post'],
        priority: 0,
    )]
    public function addPet(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
    ): Response {
        $errors = [];
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'Pet', JsonEncoder::FORMAT);
                $violations = $validator->validate($requestBodyPayload);

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
            case $requestBodyPayload instanceOf Pet:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $handler->handlePetPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        ),
                    null =>
                        $handler->handlePetPayloadToEmptyContent(
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
        path: '/pet/findByStatus',
        methods: ['get'],
        priority: 0,
    )]
    public function findPetsByStatus(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
    ): Response {
        $qStatus = strval($request->query->get('status', 'available'));
        $errors = [];
        $violations = $validator->validate(
            $qStatus,
            [
                new Assert\Choice(choices: [
                    'available',
                    'pending',
                    'sold',
                ]),
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['status'] = array_map(
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
                            $qStatus,
                        ),
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $qStatus,
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
        path: '/pet/findByTags',
        methods: ['get'],
        priority: 0,
    )]
    public function findPetsByTags(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
    ): Response {
        $qTags = strval($request->query->get('tags'));
        $errors = [];
        $violations = $validator->validate(
            $qTags,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['tags'] = array_map(
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
                            $qTags,
                        ),
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $qTags,
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
        path: '/pet/{petId}',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['get'],
        priority: 0,
    )]
    public function getPetById(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $errors = [];
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
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
                            $pPetId,
                        ),
                    null =>
                        $handler->handleEmptyPayloadToEmptyContent(
                            $pPetId,
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
        path: '/pet/{petId}',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function updatePetWithForm(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $qName = strval($request->query->get('name'));
        $qStatus = strval($request->query->get('status'));
        $errors = [];
        $violations = $validator->validate(
            $qName,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['name'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qStatus,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['status'] = array_map(
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
                            $qName,
                            $pPetId,
                            $qStatus,
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

    #[Route(
        path: '/pet/{petId}',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['delete'],
        priority: 0,
    )]
    public function deletePet(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $hApi_key = strval($request->headers->get('api_key'));
        $errors = [];
        $violations = $validator->validate(
            $hApi_key,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['api_key'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
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
                            $hApi_key,
                            $pPetId,
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

    #[Route(
        path: '/pet/{petId}/uploadImage',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function uploadFile(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PetHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $qAdditionalMetadata = strval($request->query->get('additionalMetadata'));
        $errors = [];
        $violations = $validator->validate(
            $qAdditionalMetadata,
            [
                new Assert\NotNull,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['additionalMetadata'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
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
                        $handler->handleEmptyPayloadToApplicationJsonContent(
                            $qAdditionalMetadata,
                            $pPetId,
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
