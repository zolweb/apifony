<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/mypath/{mypathparam1}')]
    public function postmypathmypathparam1(
        string $mypathparam1,
    ): Response {
        $violations = $this->validator->validate($mypathparam1, [
            new Uuid(),
        ]);

        if (count($violations) > 0) {
            throw new \Exception('Validation failed.');
        }

        $this->handler->postmypathmypathparam1(
            $mypathparam1,
        );

        return new Response('');
    }
}