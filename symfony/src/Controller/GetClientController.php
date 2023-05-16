<?php

namespace App\Controller;

use RuntimeException;
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
            'clientId' => '[^:/?#[]@!$&\\'()*+,;=]+',
            'param3' => '-?(0|[1-9]\\d*)(\\.\\d+)?([eE][+-]?\\d+)?',
            'param4' => '-?(0|[1-9]\\d*)',
            'param5' => 'true|false',
            'param1' => '[^:/?#[]@!$&\\'()*+,;=]+',
            'param2' => 'item',
        ],
        methods: ['get'],
        priority: 1,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        GetClientHandlerInterface $handler,
        string $clientId,
        float $param3,
        int $param4,
        bool $param5,
        string $param1,
        string $param2,
    ): Response {
        $pClientId = $clientId;
        $pParam3 = $param3;
        $pParam4 = $param4;
        $pParam5 = $param5;
        $pParam1 = $param1;
        $pParam2 = $param2;
        $qAgrez = floatval($request->query->get('agrez'));
        $hAzef = strval($request->headers->get('azef'));
        $cAzgrzeg = intval($request->cookies->get('azgrzeg', 10));
        $hGegzer = boolval($request->headers->get('gegzer', true));
        $errors = [];
        $violations = $validator->validate(
            $qAgrez,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['agrez'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->query->has('agrez')) {
            $errors['query']['agrez'][] = 'Parameter agrez in query is required.';
        }
        $violations = $validator->validate(
            $hAzef,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['azef'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        if (!$request->headers->has('azef')) {
            $errors['header']['azef'][] = 'Parameter azef in header is required.';
        }
        $violations = $validator->validate(
            $pClientId,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['clientId'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam3,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\LessThanOrEqual(value: 2),
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam4,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam5,
            [
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param5'] = array_map(
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
            $errors['cookie']['azgrzeg'] = array_map(
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
            $errors['header']['gegzer'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam1,
            [
                new Assert\Choice(choices: [
                    'item',
                    'item2',
                ]),
                new Format,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pParam2,
            [
                new Assert\Regex(pattern: 'item'),
                new Assert\Length(min: 1),
                new Assert\Length(max: 10),
                new Assert\Choice(choices: [
                    'item',
                    'item1',
                ]),
                new Format,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['param2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $content = json_decode($request->getContent(), true);
                $violations = $validator->validate($content, [

]);

                break;
            case 'unspecified':
                $content = null;
                $violations = [];

                break;
            default:
                return new JsonResponse(
                    [
                        'code' => 'unsupported_request_type',
                        'message' => "The value '$contentType' received in content-type header is not a supported format.",
                    ],
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                );
        }
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors['body'][$violation->getPropertyPath()][] = $violation->getMessage();
            }
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
        switch (true) {
            case is_null($content):
                return match ($request->headers->get('accept', 'unspecified')) {
                    'ApplicationJson' =>
                        $handler->handleNullApplicationJson(
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
                            $content,
                        ),
                    default =>
                        new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        ),
                };
            case is_int($content):
                return match ($request->headers->get('accept', 'unspecified')) {
                    'ApplicationJson' =>
                        $handler->handleIntegerApplicationJson(
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
                            $content,
                        ),
                    default =>
                        new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        ),
                };
            default:
                throw new RuntimeException();
        }
    }
}
