<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddPetController extends AbstractController
{
    #[Route(path: '/pet', methods: ['post'])]
    public function handle(
        Request $request,
        AddPetHandler $handler,
    ): Response {
        $body = $request->getContent();
        $response = $handler->handle(
        );

        return new Response('');
    }
}