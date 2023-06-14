<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

use App\Zol\Invoicing\Presentation\Api\Bundle\Api\DenormalizationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\ParameterValidationException;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\RequestBodyValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Api\AbstractController;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;
use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Pet;

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
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, UpdatePetApplicationJsonRequestBodyPayload::class);
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

        if (!isset(
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case $requestBodyPayload instanceOf UpdatePetApplicationJsonRequestBodyPayload:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        );

                        break;
                    case null:
                        $response = $this->handler->UpdatePetFromUpdatePetApplicationJsonRequestBodyPayloadPayloadToContent(
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
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
                try {
                    $requestBodyPayload = $this->getObjectJsonRequestBody($request, Pet::class);
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

        if (!isset(
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case $requestBodyPayload instanceOf Pet:
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->AddPetFromPetPayloadToApplicationJsonContent(
                            $requestBodyPayload,
                        );

                        break;
                    case null:
                        $response = $this->handler->AddPetFromPetPayloadToContent(
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
            case null:
                return new Response('', $response::CODE, $response->getHeaders());
            default:
                throw new RuntimeException();
        }
    }

    public function findPetsByStatus(
        Request $request,
    ): Response {
        $errors = [];

        try {
            $qStatus = $this->getStringParameter($request, 'status', 'query', false, 'available');
            $this->validateParameter(
                $qStatus,
                [
                    new Assert\NotNull,
                    new Assert\Choice(choices: [
                        'available',
                        'pending',
                        'sold',
                    ]),
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['status'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['status'] = $e->messages;
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
            $qStatus,
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->FindPetsByStatusFromEmptyPayloadToApplicationJsonContent(
                            $qStatus,
                        );

                        break;
                    case null:
                        $response = $this->handler->FindPetsByStatusFromEmptyPayloadToContent(
                            $qStatus,
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

    public function findPetsByTags(
        Request $request,
    ): Response {
        $errors = [];

        try {
            $qTags = $this->getStringParameter($request, 'tags', 'query', false);
            $this->validateParameter(
                $qTags,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['tags'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['tags'] = $e->messages;
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
            $qTags,
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->FindPetsByTagsFromEmptyPayloadToApplicationJsonContent(
                            $qTags,
                        );

                        break;
                    case null:
                        $response = $this->handler->FindPetsByTagsFromEmptyPayloadToContent(
                            $qTags,
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

    public function getPetById(
        Request $request,
        int $petId,
    ): Response {
        $errors = [];

        $pPetId = $petId;
        try {
            $this->validateParameter(
                $pPetId,
                [
                    new Assert\NotNull,
                    new AssertInt64,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['petId'] = $e->messages;
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
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->GetPetByIdFromEmptyPayloadToApplicationJsonContent(
                            $pPetId,
                        );

                        break;
                    case null:
                        $response = $this->handler->GetPetByIdFromEmptyPayloadToContent(
                            $pPetId,
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

    public function updatePetWithForm(
        Request $request,
        int $petId,
    ): Response {
        $errors = [];

        $pPetId = $petId;
        try {
            $this->validateParameter(
                $pPetId,
                [
                    new Assert\NotNull,
                    new AssertInt64,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['petId'] = $e->messages;
        }

        try {
            $qName = $this->getStringParameter($request, 'name', 'query', false);
            $this->validateParameter(
                $qName,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['name'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['name'] = $e->messages;
        }

        try {
            $qStatus = $this->getStringParameter($request, 'status', 'query', false);
            $this->validateParameter(
                $qStatus,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['status'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['status'] = $e->messages;
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
            $qName,
            $qStatus,
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->UpdatePetWithFormFromEmptyPayloadToContent(
                            $pPetId,
                            $qName,
                            $qStatus,
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

    public function deletePet(
        Request $request,
        int $petId,
    ): Response {
        $errors = [];

        $pPetId = $petId;
        try {
            $this->validateParameter(
                $pPetId,
                [
                    new Assert\NotNull,
                    new AssertInt64,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['petId'] = $e->messages;
        }

        try {
            $hApi_key = $this->getStringParameter($request, 'api_key', 'header', false);
            $this->validateParameter(
                $hApi_key,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['header']['api_key'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['header']['api_key'] = $e->messages;
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
            $hApi_key,
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case null:
                        $response = $this->handler->DeletePetFromEmptyPayloadToContent(
                            $hApi_key,
                            $pPetId,
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

    public function uploadFile(
        Request $request,
        int $petId,
    ): Response {
        $errors = [];

        $pPetId = $petId;
        try {
            $this->validateParameter(
                $pPetId,
                [
                    new Assert\NotNull,
                    new AssertInt64,
                ],
            );
        } catch (ParameterValidationException $e) {
            $errors['path']['petId'] = $e->messages;
        }

        try {
            $qAdditionalMetadata = $this->getStringParameter($request, 'additionalMetadata', 'query', false);
            $this->validateParameter(
                $qAdditionalMetadata,
                [
                    new Assert\NotNull,
                ],
            );
        } catch (DenormalizationException $e) {
            $errors['query']['additionalMetadata'] = [$e->getMessage()];
        } catch (ParameterValidationException $e) {
            $errors['query']['additionalMetadata'] = $e->messages;
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
            $qAdditionalMetadata,
            $requestBodyPayload,
        )) {
            throw new RuntimeException('All parameter variables should be initialized at the time.');
        }

        $responsePayloadContentType = $request->headers->get('accept');
        switch (true) {
            case is_null($requestBodyPayload):
                switch($responsePayloadContentType) {
                    case 'application/json':
                        $response = $this->handler->UploadFileFromEmptyPayloadToApplicationJsonContent(
                            $pPetId,
                            $qAdditionalMetadata,
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
