<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostClientClientIdParam1Param2Param3Param4Param5Param6Controller extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/{param1}/{param2}/{param3}/{param4}/{param5}/{param6}',
        requirements: [
            'pClientId' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam1' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam2' => 'a-Z',
            'pParam3' => '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?',
            'pParam4' => '-?(0|[1-9]\d*)',
            'pParam5' => 'true|false',
            'pParam6' => '[^:/?#[]@!$&\'()*+,;=]+',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PostClientClientIdParam1Param2Param3Param4Param5Param6HandlerInterface $handler,
        string $pClientId,
        mixed $pParam1,
        string $pParam2 = 'default',
        float $pParam3 = 5.3E-7,
        int $pParam4,
        bool $pParam5,
        array $pParam6,
    ): Response {
        $errors = [];
        $violations = $validator->validate(
            $pClientId,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pClientId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam1,
            [
                new Assert\NotNull(),
                new Format(),
                new Assert\Choice(['item', 'item2']),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam2,
            [
                new Assert\NotNull(),
                new Format(),
                new Assert\Regex('/a-Z/'),
                new Assert\Length(min: 1),
                new Assert\Length(max: 10),
                new Assert\Choice(['item', 'item1']),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam3,
            [
                new Assert\NotNull(),
                new Assert\GreaterThan(1),
                new Assert\LessThan(2),
                new Assert\Choice(['1', '2']),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam4,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam5,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam5'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam6,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pParam6'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (count($errors) > 0) {
            return new JsonResponse(
                [
                    'code' => 'validation_failed',
                    'message' => 'Validation has failed.',
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }
        return $handler->handle(
            $pClientId,
            $pParam1,
            $pParam2,
            $pParam3,
            $pParam4,
            $pParam5,
            $pParam6,
        );
    }
}

// $contentType = $request->headers->get('accept');
// if ($contentType !== 'application/json') {
// return new \Symfony\Component\HttpFoundation\JsonResponse(
// [
// 'code' => 'not_acceptable_format',
// 'message' => "The value '$contentType' received in accept header is not an acceptable format.",
// ],
// \Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE,
// );
// }
