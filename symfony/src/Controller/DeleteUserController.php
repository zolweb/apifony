<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteUserController extends AbstractController
{
    #[Route(path: '/user/{username}', methods: ['delete'])]
    public function handle(
        Request $request,
        DeleteUserHandler $handler,
        string $username,
    ): Response {
        $response = $handler->handle(
            $username,
        );

        return new Response('');
    }
}