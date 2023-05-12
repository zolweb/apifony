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

class GetClientController extends AbstractController
{
    #[Route(
        path: '/client/{clientId}/{param1}/{param2}/{param3}/{param4}/{param5}',
        requirements: [
            'pClientId' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam3' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam4' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam5' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam1' => '[^:/?#[]@!$&\'()*+,;=]+',
            'pParam2' => '[^:/?#[]@!$&\'()*+,;=]+',
        ],
        methods: ['get'],
        priority: 1,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        GetClientHandlerInterface $handler,
        ?mixed $pClientId,
        ?mixed $pParam3,
        ?mixed $pParam4,
        ?mixed $pParam5,
        ?mixed $pParam1,
        ?mixed $pParam2 = 'item',
    ): Response {
        $qAgrez = floatval($request->query->get('agrez', null));
        $hAzef = ($request->headers->get('azef', null));
        $cAzgrzeg = intval($request->cookies->get('azgrzeg', 10));
        $hGegzer = boolval($request->headers->get('gegzer', 1));
        $errors = [];
        $violations = $validator->validate(
            $qAgrez,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qAgrez'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hAzef,
            [
                new Assert\NotNull(),
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hAzef'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
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
            $pParam3,
            [
                new Assert\NotNull(),
                new Assert\LessThanOrEqual(2),
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
            $cAzgrzeg,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['cAzgrzeg'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hGegzer,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hGegzer'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam1,
            [
                new Assert\NotNull(),
                new Lol(),
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
                new Lol(),
                new Assert\Regex('/item/'),
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
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $content = $request->getContent();
                $payload = $serializer->deserialize($content, Lol::class, JsonEncoder::FORMAT);
                $violations = $validator->validate($payload);
                if (count($violations) > 0) {
                    foreach ($violations as $violation) {
                        $errors['body'][$violation->getPropertyPath()][] = $violation->getMessage();
                    }
                }

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_format',
                        'message' => "The value '$contentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
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
            $qAgrez,
            $hAzef,
            $pClientId,
            $pParam3,
            $pParam4,
            $pParam5,
            $cAzgrzeg,
            $hGegzer,
            $pParam1,
            $pParam2,
            $payload,
        );
    }
}
