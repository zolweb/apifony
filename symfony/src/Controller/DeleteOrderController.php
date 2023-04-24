<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteOrderController extends AbstractController
{
    #[Route(path: '/store/order/{orderId}', methods: ['delete'])]
    public function handle(
        Request $request,
        DeleteOrderHandler $handler,
        int $orderId,
    ): Response {
        $response = $handler->handle(
            $orderId,
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
