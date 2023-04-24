<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdatePetWithFormController extends AbstractController
{
    #[Route(path: '/pet/{petId}', methods: ['post'])]
    public function handle(
        Request $request,
        UpdatePetWithFormHandler $handler,
        int $petId,
    ): Response {
        $name = $request->query->get('name');
        $status = $request->query->get('status');
        $response = $handler->handle(
            $petId,
            $name,
            $status,
        );

        return new Response('');
    }
}