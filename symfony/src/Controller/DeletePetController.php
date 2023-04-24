<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeletePetController extends AbstractController
{
    #[Route(path: '/pet/{petId}', methods: ['delete'])]
    public function handle(
        Request $request,
        DeletePetHandler $handler,
        int $petId,
    ): Response {
        $response = $handler->handle(
            $api_key,
            $petId,
        );

        return new Response('');
    }
}