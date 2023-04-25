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
            'clientId' => '.+',
            'param1' => '.+',
            'param2' => '.+',
            'param3' => '\d+',
            'param4' => '\d+',
            'param5' => 'true|false',
            'param6' => '.+',
        ],
        methods: ['post'],
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PostClientClientIdParam1Param2Param3Param4Param5Param6Handler $handler,
        string $clientId,
        mixed $param1,
        string $param2,
        float $param3,
        int $param4,
        bool $param5,
        array $param6,
    ): Response {
        $errors = [];
        $violations = $validator->validate(
            $clientId,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['clientId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param1,
            [
                new Assert\NotNull(),
                new Format(),
                new Assert\Choice(['item', 'item2']),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param2,
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
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param3,
            [
                new Assert\NotNull(),
                new Assert\GreaterThan(1),
                new Assert\LessThan(2),
                new Assert\Choice(['1', '2']),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param4,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param5,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param5'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $param6,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param6'] = array_map(
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
        $handler->handle(
            $clientId,
            $param1,
            $param2,
            $param3,
            $param4,
            $param5,
            $param6,
        );
        return new Response('');
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
