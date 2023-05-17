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

class DeleteOrderController extends AbstractController
{
    #[Route(
        path: '/store/order/{orderId}',
        requirements: [
            'orderId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['delete'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DeleteOrderHandlerInterface $handler,
        int $orderId,
    ): Response {
        $pOrderId = $orderId;
        $errors = [];
        $violations = $validator->validate(
            $pOrderId,
            [
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
        switch (true) {
            case is_null($content):
                $responseContent = match ($request->headers->get('accept', 'unspecified')) {
                    default =>
                        new class {
                            public readonly string $code;
                            public readonly array $content;
                            public readonly array $headers;
                            public function __construct()
                            {
                                $this->code = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
                                $this->content = [
                                    'code' => 'unsupported_response_type',
                                    'message' => "The value '$contentType' received in accept header is not a supported format.",
                                ];
                                $this->headers = [];
                            }
                        },
                };
            default:
                throw new RuntimeException();
        }
    }
}
