<?php

namespace App\Controller;

class DeletePetController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route(path: '/pet/{petId}', methods: ['delete'])]
    public function handle(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\Serializer\SerializerInterface $serializer,
        DeletePetHandler $handler,
        int $petId,
    ): \Symfony\Component\HttpFoundation\Response {
        $response = $handler->handle(
            $api_key,
            $petId,
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
