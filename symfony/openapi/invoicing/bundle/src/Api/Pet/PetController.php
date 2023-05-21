<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\AbstractController;

class PetController extends AbstractController
{
    private PetHandler $handler;

    public function setHandler(PetHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function updatePet(
        Request $request,
    ): Response {
        $errors = [];
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'UpdatePetApplicationJsonMediaTypeSchema', JsonEncoder::FORMAT);
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
            case $requestBodyPayload instanceOf UpdatePetApplicationJsonMediaTypeSchema:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $this->handler->updatePetFromUpdatePetApplicationJsonMediaTypeSchemaPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        ),
                    null =>
                        $this->handler->updatePetFromUpdatePetApplicationJsonMediaTypeSchemaPayloadToEmptyContent(
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

    public function addPet(
        Request $request,
    ): Response {
        $errors = [];
        switch ($requestBodyPayloadContentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $requestBodyPayload = $serializer->deserialize($request->getContent(), 'Pet', JsonEncoder::FORMAT);
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
            case $requestBodyPayload instanceOf Pet:
                $responsePayload = match ($responsePayloadContentType) {
                    'application/json' =>
                        $this->handler->addPetFromPetPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        ),
                    null =>
                        $this->handler->addPetFromPetPayloadToEmptyContent(
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

    public function findPetsByStatus(
        Request $request,
    ): Response {
        $qStatus = strval($request->query->get('status', 'available'));
        $errors = [];
        $violations = $this->validator->validate(
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
                        $this->handler->findPetsByStatusFromEmptyPayloadToApplicationJsonContent(
                            $qStatus,
                        ),
                    null =>
                        $this->handler->findPetsByStatusFromEmptyPayloadToEmptyContent(
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

    public function findPetsByTags(
        Request $request,
    ): Response {
        $qTags = strval($request->query->get('tags'));
        $errors = [];
        $violations = $this->validator->validate(
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
                        $this->handler->findPetsByTagsFromEmptyPayloadToApplicationJsonContent(
                            $qTags,
                        ),
                    null =>
                        $this->handler->findPetsByTagsFromEmptyPayloadToEmptyContent(
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

    public function getPetById(
        Request $request,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $errors = [];
        $violations = $this->validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new AssertFormat\Int64,
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
                        $this->handler->getPetByIdFromEmptyPayloadToApplicationJsonContent(
                            $pPetId,
                        ),
                    null =>
                        $this->handler->getPetByIdFromEmptyPayloadToEmptyContent(
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

    public function updatePetWithForm(
        Request $request,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $qName = strval($request->query->get('name'));
        $qStatus = strval($request->query->get('status'));
        $errors = [];
        $violations = $this->validator->validate(
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
        $violations = $this->validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new AssertFormat\Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $this->validator->validate(
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
                        $this->handler->updatePetWithFormFromEmptyPayloadToEmptyContent(
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

    public function deletePet(
        Request $request,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $hApi_key = strval($request->headers->get('api_key'));
        $errors = [];
        $violations = $this->validator->validate(
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
        $violations = $this->validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new AssertFormat\Int64,
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
                        $this->handler->deletePetFromEmptyPayloadToEmptyContent(
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

    public function uploadFile(
        Request $request,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $qAdditionalMetadata = strval($request->query->get('additionalMetadata'));
        $errors = [];
        $violations = $this->validator->validate(
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
        $violations = $this->validator->validate(
            $pPetId,
            [
                new Assert\NotNull,
                new AssertFormat\Int64,
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
                        $this->handler->uploadFileFromEmptyPayloadToApplicationJsonContent(
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
