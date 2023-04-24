<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateUsersWithListInputController extends AbstractController
{
    #[Route(path: '/user/createWithList', methods: ['post'])]
    public function handle(
        Request $request,
        CreateUsersWithListInputHandler $handler,
    ): Response {
        $body = $request->getContent();
        $response = $handler->handle(
        );

        return new Response('');
    }
}