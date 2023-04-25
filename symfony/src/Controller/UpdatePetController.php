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

class UpdatePetController extends AbstractController
{
    #[Route(
        path: '/pet',
        requirements: [
        ],
        methods: ['put'],
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UpdatePetHandler $handler,
    ): Response {
        $errors = [];
        $contentType = $request->headers->get('content-type');
        if ($contentType !== 'application/json') {
            return new JsonResponse(
                [
                    'code' => 'unsupported_format',
                    'message' => "The value '$contentType' received in content-type header is not a supported format.",
                ],
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            );
        }
        $content = $request->getContent();
        $dto = $serializer->deserialize($content, PetSchema::class, JsonEncoder::FORMAT);
        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
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
            $dto,
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
