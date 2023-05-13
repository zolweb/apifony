<?php

namespace App\Controller;

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

class UploadFileController extends AbstractController
{
    #[Route(
        path: '/pet/{petId}/uploadImage',
        requirements: [
            'pPetId' => '[^:/?#[]@!$&\'()*+,;=]+',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UploadFileHandlerInterface $handler,
        ?mixed $pPetId,
    ): Response {
        $qAdditionalMetadata = ($request->query->get('additionalMetadata', null));
        $errors = [];
        $violations = $validator->validate(
            $qAdditionalMetadata,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qAdditionalMetadata'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pPetId,
            [
                new Assert\NotNull(),
                new Lol(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pPetId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_format',
                        'message' => "The value '$contentType' received in content-type header is not a supported format.",
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
        return $handler->handle(
            $qAdditionalMetadata,
            $pPetId,
            $payload,
        );
    }
}
