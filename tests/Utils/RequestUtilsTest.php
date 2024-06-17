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

    public function getContextFromHostProvider(): array
    {
        return [
            'localhost' => ['app', 'localhost'],
            'localhost admin' => ['admin', 'admin.localhost'],
            'localhost app' => ['app', 'app.localhost'],
            'localhost partenaire' => ['partenaire', 'partenaire.pro.localhost'],
            // MDT pour démontrer qu'il faut un sous domaine supplémentaire comme le test du dessus dans le cas du localhost car pas d'extension .fr
            'localhost fallback sur app' => ['app', 'partenaire.localhost'],
            'domain custom' => ['app', 'domain.fr', 'domain.fr'],
            'domain custom sous domaine admin' => ['admin', 'admin.domain.fr', 'domain.fr'],
            'domain custom sous domaine app' => ['app', 'app.domain.fr', 'domain.fr'],
            'domain custom sous domaine partenaire' => ['partenaire', 'partenaire.domain.fr', 'domain.fr'],
            'domain custom multi sous domaine' => ['foo', 'foo.bar.domain.fr', 'domain.fr'],
            'domain variante sans sous domaine' => ['app', 'domain-variante.fr', 'domain.fr'],
            'domain variante avec sous domaine partenaire' => ['partenaire', 'partenaire.domain-variante.fr', 'domain.fr'],
            'domain variante multi sous domaine' => ['foo', 'foo.bar.domain-variante.fr', 'domain.fr'],
        ];
    }
}
