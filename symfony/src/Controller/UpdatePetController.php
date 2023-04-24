<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdatePetController extends AbstractController
{
    #[Route(path: '/pet', methods: ['put'])]
    public function handle(
        Request $request,
        UpdatePetHandler $handler,
    ): Response {
        $body = $request->getContent();
        $response = $handler->handle(
        );

        return new Response('');
    }
}