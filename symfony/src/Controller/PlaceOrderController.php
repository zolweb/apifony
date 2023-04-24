<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceOrderController extends AbstractController
{
    #[Route(path: '/store/order', methods: ['post'])]
    public function handle(
        Request $request,
        PlaceOrderHandler $handler,
    ): Response {
        $body = $request->getContent();
        $response = $handler->handle(
        );

        return new Response('');
    }
}