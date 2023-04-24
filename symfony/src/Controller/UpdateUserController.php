<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateUserController extends AbstractController
{
    #[Route(path: '/user/{username}', methods: ['put'])]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        UpdateUserHandler $handler,
        string $username,
    ): Response {
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
        $dto = $serializer->deserialize($content, UserSchema::class, JsonEncoder::FORMAT);
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
