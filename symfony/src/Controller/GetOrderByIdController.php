<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetOrderByIdController extends AbstractController
{
    #[Route(path: '/store/order/{orderId}', methods: ['get'])]
    public function handle(
        Request $request,
        GetOrderByIdHandler $handler,
        int $orderId,
    ): Response {
        $response = $handler->handle(
            $orderId,
        );

        return new Response('');
    }
}