<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateUserController extends AbstractController
{
    #[Route(path: '/user/{username}', methods: ['put'])]
    public function handle(
        Request $request,
        UpdateUserHandler $handler,
        string $username,
    ): Response {
        $body = $request->getContent();
        $response = $handler->handle(
            $username,
        );

        return new Response('');
    }
}