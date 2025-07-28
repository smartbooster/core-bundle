<?php

namespace Smart\CoreBundle\Tests\Validator\Constraints;

use Smart\CoreBundle\AbstractValidatorTest;
use Smart\CoreBundle\Validator\Constraints\IsModulo;
use Smart\CoreBundle\Validator\Constraints\IsModuloValidator;

/**
 * vendor/bin/simple-phpunit tests/Validator/Constraints/IsModuloValidatorTest.php
 */
class IsModuloValidatorTest extends AbstractValidatorTest
{
    private const INVALID_MESSAGE = 'is_modulo.error';

    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new IsModuloValidator();
    }

    /**
     * @dataProvider failProvider
     */
    public function testValidationFail(int $value): void
    {
        $constraint = new IsModulo(15);
        $validator = $this->initValidator(self::INVALID_MESSAGE);

        $validator->validate($value, $constraint); // @phpstan-ignore-line
    }

    public static function failProvider(): array
    {
        return [
            '1' => [1],
            '5' => [5],
            '14' => [14],
            '16' => [16],
            '44' => [44],
            '46' => [46],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testValidationOk(int $value): void
    {
        $constraint = new IsModulo(15);
        $validator = $this->initValidator();

        $validator->validate($value, $constraint); // @phpstan-ignore-line
    }

    public static function validProvider(): array
    {
        return [
            '0' => [0],
            '15' => [15],
            '30' => [30],
            '120' => [120],
        ];
    }
}
