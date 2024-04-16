<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\Zol\Ogen;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OgenTest extends WebTestCase
{
    public function testGetAtom(): void
    {
        $httpClient = self::createClient();
        $httpClient->catchExceptions(false);

        $httpClient->request(
            method: 'GET',
            uri: '/atoms/hydrogen',
        );

        self::assertResponseStatusCodeSame(200);
    }
}