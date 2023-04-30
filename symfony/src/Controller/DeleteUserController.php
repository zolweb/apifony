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

class DeleteUserController extends AbstractController
{
    #[Route(
        path: '/user/{username}',
        requirements: [
            'username' => '[^:/?#[]@!$&\'()*+,;=]+',
        ],
        methods: ['delete'],
        priority: 0,    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DeleteUserHandlerInterface $handler,
        string $username,
    ): Response {
        $errors = [];
        $violations = $validator->validate(
            $username,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['username'] = array_map(
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
        $handler->handle(
            $username,
        );
        return new Response('');
    }
}

// $contentType = $request->headers->get('accept');
// if ($contentType !== 'application/json') {
// return new \Symfony\Component\HttpFoundation\JsonResponse(
// [
// 'code' => 'not_acceptable_format',
// 'message' => "The value '$contentType' received in accept header is not an acceptable format.",
// ],
// \Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE,
// );
// }
