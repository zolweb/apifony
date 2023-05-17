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

class FindPetsByTagsController extends AbstractController
{
    #[Route(
        path: '/pet/findByTags',
        methods: ['get'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FindPetsByTagsHandlerInterface $handler,
    ): Response {
        $qTags = strval($request->query->get('tags'));
        $errors = [];
        $violations = $validator->validate(
            $qTags,
            [
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
        $responseContentType = $request->headers->get('accept', 'unspecified');
        switch (true) {
            case is_null($content): // @phpstan-ignore-line
                $responseContent = match ($responseContentType) {
                    'ApplicationJson' =>
                        $handler->handleEmptyApplicationJson(
                            $qTags,
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
