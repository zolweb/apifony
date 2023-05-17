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

class UploadFileController extends AbstractController
{
    #[Route(
        path: '/pet/{petId}/uploadImage',
        requirements: [
            'petId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UploadFileHandlerInterface $handler,
        int $petId,
    ): Response {
        $pPetId = $petId;
        $qAdditionalMetadata = strval($request->query->get('additionalMetadata'));
        $errors = [];
        $violations = $validator->validate(
            $qAdditionalMetadata,
            [
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
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['petId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            case 'unspecified':
                $content = null;
                $violations = [];

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$contentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }
        if (count($violations) > 0) { // @phpstan-ignore-line
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
        $responseContentType = $request->headers->get('accept', 'unspecified');
        switch (true) {
            case is_null($content): // @phpstan-ignore-line
                $responseContent = match ($responseContentType) {
                    'ApplicationJson' =>
                        $handler->handleEmptyApplicationJson(
                            $qAdditionalMetadata,
                            $pPetId,
                        ),
                    default =>
                        new class ($responseContentType) {
                            public readonly int $code;
                            /** @var array{code: string, message: string} */
                            public readonly array $content;
                            public function __construct(string $responseContentType)
                            {
                                $this->code = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
                                $this->content = [
                                    'code' => 'unsupported_response_type',
                                    'message' => "The value '$responseContentType' received in accept header is not a supported format.",
                                ];
                            }
                        },
                };
            default:
                throw new RuntimeException();
        }
    }
}
