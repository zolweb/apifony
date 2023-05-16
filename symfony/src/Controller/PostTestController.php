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

class PostTestController extends AbstractController
{
    #[Route(
        path: '/test/{p1}/{p2}/{p3}/{p4}',
        requirements: [
            'pP1' => '[a-z]{3}',
            'pP2' => '-?(0|[1-9]\\d*)',
            'pP3' => '-?(0|[1-9]\\d*)(\\.\\d+)?([eE][+-]?\\d+)?',
            'pP4' => 'true|false',
        ],
        methods: ['post'],
        priority: 0,
    )]
    public function handle(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PostTestHandlerInterface $handler,
        string $pP1,
        int $pP2,
        float $pP3,
        bool $pP4,
    ): Response {
        $cC1 = strval($request->cookies->get('c1', 'abc'));
        $cC2 = intval($request->cookies->get('c2', 1));
        $cC3 = floatval($request->cookies->get('c3', 0.1));
        $cC4 = boolval($request->cookies->get('c4', true));
        $hH1 = strval($request->headers->get('h1', 'abc'));
        $hH2 = intval($request->headers->get('h2', 1));
        $hH3 = floatval($request->headers->get('h3', 0.1));
        $hH4 = boolval($request->headers->get('h4', true));
        $qQ1 = strval($request->query->get('q1', 'abc'));
        $qQ2 = intval($request->query->get('q2', 1));
        $qQ3 = floatval($request->query->get('q3', 0.1));
        $qQ4 = boolval($request->query->get('q4', true));
        $errors = [];
        $violations = $validator->validate(
            $cC1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new F13,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['cC1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $cC2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new F14,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['cC2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $cC3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new F15,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['cC3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $cC4,
            [
                new F16,
            ]
        );
        if (count($violations) > 0) {
            $errors['cookie']['cC4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hH1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new F5,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hH1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hH2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new F6,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hH2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hH3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new F7,
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hH3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $hH4,
            [
                new Assert\Choice(choices: [
                    true,
                ]),
            ]
        );
        if (count($violations) > 0) {
            $errors['header']['hH4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new F1,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pP1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThan(value: 4),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new F2,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pP2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\GreaterThan(value: -1),
                new Assert\Choice(choices: [
                    0.2,
                    0.3,
                    0.1,
                ]),
                new F3,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pP3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $pP4,
            [
                new Assert\Choice(choices: [
                    true,
                ]),
                new F4,
            ]
        );
        if (count($violations) > 0) {
            $errors['path']['pP4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qQ1,
            [
                new Assert\Regex(pattern: '[a-z]{3}'),
                new Assert\Length(min: 3),
                new Assert\Length(max: 3),
                new Assert\Choice(choices: [
                    'abc',
                    'def',
                    'ghi',
                ]),
                new F9,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qQ1'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qQ2,
            [
                new Assert\DivisibleBy(value: 1),
                new Assert\GreaterThanOrEqual(value: 1),
                new Assert\LessThanOrEqual(value: 3),
                new Assert\Choice(choices: [
                    1,
                    2,
                    3,
                ]),
                new F10,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qQ2'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qQ3,
            [
                new Assert\DivisibleBy(value: 0.1),
                new Assert\GreaterThanOrEqual(value: 0),
                new Assert\LessThanOrEqual(value: 1),
                new Assert\Choice(choices: [
                    0.1,
                    0.2,
                    0.3,
                ]),
                new F13,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qQ3'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        $violations = $validator->validate(
            $qQ4,
            [
                new F12,
            ]
        );
        if (count($violations) > 0) {
            $errors['query']['qQ4'] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
        switch ($contentType = $request->headers->get('content-type', 'unspecified')) {
            case 'application/json':
                $content = $serializer->deserialize($request->getContent(), 'Test', JsonEncoder::FORMAT);
                $violations = $validator->validate($content);

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
                switch ($contentType = $request->headers->get('accept', 'unspecified')) {
                    case Test:
                        return $handler->handle(
                                $cC1,
                                $cC2,
                                $cC3,
                                $cC4,
                                $hH1,
                                $hH2,
                                $hH3,
                                $hH4,
                                $pP1,
                                $pP2,
                                $pP3,
                                $pP4,
                                $qQ1,
                                $qQ2,
                                $qQ3,
                                $qQ4,
                                $content,
                        );

                        break;
                    case Test:
                        return $handler->handle(
                                $cC1,
                                $cC2,
                                $cC3,
                                $cC4,
                                $hH1,
                                $hH2,
                                $hH3,
                                $hH4,
                                $pP1,
                                $pP2,
                                $pP3,
                                $pP4,
                                $qQ1,
                                $qQ2,
                                $qQ3,
                                $qQ4,
                                $content,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
            case $content instanceOf Test:
                switch ($contentType = $request->headers->get('accept', 'unspecified')) {
                    case Test:
                        return $handler->handle(
                                $cC1,
                                $cC2,
                                $cC3,
                                $cC4,
                                $hH1,
                                $hH2,
                                $hH3,
                                $hH4,
                                $pP1,
                                $pP2,
                                $pP3,
                                $pP4,
                                $qQ1,
                                $qQ2,
                                $qQ3,
                                $qQ4,
                                $content,
                        );

                        break;
                    case Test:
                        return $handler->handle(
                                $cC1,
                                $cC2,
                                $cC3,
                                $cC4,
                                $hH1,
                                $hH2,
                                $hH3,
                                $hH4,
                                $pP1,
                                $pP2,
                                $pP3,
                                $pP4,
                                $qQ1,
                                $qQ2,
                                $qQ3,
                                $qQ4,
                                $content,
                        );

                        break;
                    default:
                        return new JsonResponse(
                            [
                                'code' => 'unsupported_response_type',
                                'message' => "The value '$contentType' received in accept header is not a supported format.",
                            ],
                            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                        );
                }

                break;
        }
    }
}
