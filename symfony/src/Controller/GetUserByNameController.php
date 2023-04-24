<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetUserByNameController extends AbstractController
{
    #[Route(path: '/user/{username}', methods: ['get'])]
    public function handle(
        Request $request,
        GetUserByNameHandler $handler,
        string $username,
    ): Response {
        $response = $handler->handle(
            $username,
        );

        return new Response('');
    }
}