<?php

namespace Smart\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
abstract class AbstractFixtures extends Fixture
{
    protected string $fixturesDir;

    public function __construct(protected ParameterBagInterface $parameterBag, protected PurgerLoader $loader)
    {
        $this->fixturesDir = $parameterBag->get('kernel.project_dir') . '/fixtures/';
    }
}
