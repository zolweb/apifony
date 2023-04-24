<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadFileController extends AbstractController
{
    #[Route(path: '/pet/{petId}/uploadImage', methods: ['post'])]
    public function handle(
        Request $request,
        UploadFileHandler $handler,
        int $petId,
    ): Response {
        $additionalMetadata = $request->query->get('additionalMetadata');
        $body = $request->getContent();
        $response = $handler->handle(
            $petId,
            $additionalMetadata,
        );

        return new Response('');
    }
}