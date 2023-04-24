<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FindPetsByTagsController extends AbstractController
{
    #[Route(path: '/pet/findByTags', methods: ['get'])]
    public function handle(
        Request $request,
        FindPetsByTagsHandler $handler,
    ): Response {
        $tags = $request->query->get('tags');
        $response = $handler->handle(
            $tags,
        );

        return new Response('');
    }
}