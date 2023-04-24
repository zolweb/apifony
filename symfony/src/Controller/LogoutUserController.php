<?php

namespace App\Controller;

class LogoutUserController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route(path: '/user/logout', methods: ['get'])]
    public function handle(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Serializer\SerializerInterface $serializer,
        LogoutUserHandler $handler,
    ): \Symfony\Component\HttpFoundation\Response {
        $handler->handle(
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
