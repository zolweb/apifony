<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetPetByIdController extends AbstractController
{
    #[Route(path: '/pet/{petId}', methods: ['get'])]
    public function handle(
        Request $request,
        GetPetByIdHandler $handler,
        int $petId,
    ): Response {
        $response = $handler->handle(
            $petId,
        );

        return new Response('');
    }
}