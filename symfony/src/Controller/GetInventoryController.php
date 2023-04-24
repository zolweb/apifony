<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetInventoryController extends AbstractController
{
    #[Route(path: '/store/inventory', methods: ['get'])]
    public function handle(
        Request $request,
        GetInventoryHandler $handler,
    ): Response {
        $response = $handler->handle(
        );

        return new Response('');
    }
}

// $contentType = $request->headers->get('accept');
// if ($contentType !== 'application/json') {
// return new JsonResponse(
// [
// 'code' => 'not_acceptable_format',
// 'message' => "The value '$contentType' received in accept header is not an acceptable format.",
// ],
// Response::HTTP_NOT_ACCEPTABLE,
// );
// }
