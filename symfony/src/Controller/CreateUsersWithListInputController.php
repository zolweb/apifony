<?php

namespace App\Controller;

class CreateUsersWithListInputController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route(path: '/user/createWithList', methods: ['post'])]
    public function handle(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Serializer\SerializerInterface $serializer,
        CreateUsersWithListInputHandler $handler,
    ): \Symfony\Component\HttpFoundation\Response {
        $contentType = $request->headers->get('content-type');
        if ($contentType !== 'application/json') {
            return new \Symfony\Component\HttpFoundation\JsonResponse(
                [
                    'code' => 'unsupported_format',
                    'message' => "The value '$contentType' received in content-type header is not a supported format.",
                ],
                \Symfony\Component\HttpFoundation\Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            );
        }
        $content = $request->getContent();
        $response = $handler->handle(
        );

        return new \Symfony\Component\HttpFoundation\Response('');
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
