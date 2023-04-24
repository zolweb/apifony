<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutUserController extends AbstractController
{
    #[Route(path: '/user/logout', methods: ['get'])]
    public function handle(
        Request $request,
        LogoutUserHandler $handler,
    ): Response {
        $response = $handler->handle(
        );

        return new Response('');
    }
}