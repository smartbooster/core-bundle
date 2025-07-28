<?php

namespace Smart\CoreBundle\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Smart\CoreBundle\Utils\RequestUtils;

/**
 * vendor/bin/simple-phpunit tests/Utils/RequestUtilsTest.php
 */
class RequestUtilsTest extends TestCase
{
    /**
     * @dataProvider getContextFromHostProvider
     */
    public function testGetContextFromHost(string $expected, string $host, ?string $domain = null): void
    {
        $this->assertEquals($expected, RequestUtils::getContextFromHost($host, $domain));
    }

    public static function getContextFromHostProvider(): array
    {
        return [
            'localhost' => ['app', 'localhost'],
            'localhost admin' => ['admin', 'admin.localhost'],
            'localhost app' => ['app', 'app.localhost'],
            'localhost account' => ['account', 'account.localhost'],
            'localhost partenaire' => ['partenaire', 'partenaire.pro.localhost'],
            'localhost fallback with app if no ending extension and different than localhost' => ['app', 'domain'],
            'custom domain' => ['app', 'domain.fr', 'domain.fr'],
            'custom domain subdomain admin' => ['admin', 'admin.domain.fr', 'domain.fr'],
            'custom domain subdomain app' => ['app', 'app.domain.fr', 'domain.fr'],
            'custom domain subdomain partenaire' => ['partenaire', 'partenaire.domain.fr', 'domain.fr'],
            'multi custom domain with subdomain' => ['foo', 'foo.bar.domain.fr', 'domain.fr'],
            'variant domain without subdomain' => ['app', 'domain-variante.fr', 'domain.fr'],
            'variant domain with subdomain partenaire' => ['partenaire', 'partenaire.domain-variante.fr', 'domain.fr'],
            'variant domain with multiple subdomain' => ['foo', 'foo.bar.domain-variante.fr', 'domain.fr'],
        ];
    }
}
