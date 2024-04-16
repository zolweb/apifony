<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\Zol\Ogen;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OgenTest extends WebTestCase
{
    public function testA(): void
    {
        $httpClient = self::createClient();
        $httpClient->catchExceptions(false);

        $httpClient->jsonRequest(
            method: 'POST',
            uri: '/admin/contents',
            parameters: [
                'contentTypeId' => 'atom',
                'name' => 'Hydrogen',
                'localeId' => 'en',
                'data' => [],
            ],
        );
    }
}