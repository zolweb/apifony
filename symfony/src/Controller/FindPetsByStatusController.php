<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FindPetsByStatusController extends AbstractController
{
    #[Route(path: '/pet/findByStatus', methods: ['get'])]
    public function handle(
        Request $request,
        FindPetsByStatusHandler $handler,
    ): Response {
        $status = $request->query->get('status');
        $response = $handler->handle(
            $status,
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
