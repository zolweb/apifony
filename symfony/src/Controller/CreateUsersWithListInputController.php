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

class CreateUsersWithListInputController extends AbstractController
{
    #[Route(
        path: '/user/createWithList',
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CreateUsersWithListInputHandlerInterface $handler,
    ): Response {
        $errors = [];
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $content = $request->getContent();
                $payload = $serializer->deserialize($content, Lol::class, JsonEncoder::FORMAT);
                $violations = $validator->validate($payload);
                if (count($violations) > 0) {
                    foreach ($violations as $violation) {
                        $errors['body'][$violation->getPropertyPath()][] = $violation->getMessage();
                    }
                }

                break;
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
            $payload,
        );
    }
}
