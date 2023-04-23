<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Postmypathmypathparam1Controller extends AbstractController
{
    public function __construct(
        private readonly Postmypathmypathparam1Handler $handler,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route(path: '/mypath/{mypathparam1}', methods: ['post'], priority: 0)]
    public function postmypathmypathparam1(
        string $mypathparam1,
    ): Response {
        $violations = $this->validator->validate($mypathparam1, [
            new Uuid(),
        ]);

        if (count($violations) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $this->handler->postmypathmypathparam1(
            $mypathparam1,
        );

        return new Response('');
    }
}