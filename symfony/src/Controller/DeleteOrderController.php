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

class DeleteOrderController extends AbstractController
{
    #[Route(
        path: '/store/order/{orderId}',
        requirements: [
            'pOrderId' => '-?(0|[1-9]\\d*)',
        ],
        methods: ['delete'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DeleteOrderHandlerInterface $handler,
        int $pOrderId,
    ): Response {
        $errors = [];
        $violations = $validator->validate(
            $pOrderId,
            [
                new Int64,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pOrderId'] = array_map(
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
                switch ($contentType = $request->headers->get('accept', 'unspecified')) {
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
        }
    }
}
