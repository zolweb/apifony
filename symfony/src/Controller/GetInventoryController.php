<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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