<?php

namespace Smart\CoreBundle\Tests\Validator\Constraints;

use Smart\CoreBundle\Utils\RegexUtils;
use Smart\StandardBundle\Validator\Constraints\AbstractValidatorTest;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

/**
 * vendor/bin/simple-phpunit tests/Validator/Constraints/RegexValidatorTest.php
 */
class RegexValidatorTest extends AbstractValidatorTest
{
    private const INVALID_MESSAGE = 'This value is not valid.';

    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new RegexValidator();
    }

    /**
     * @dataProvider validPhoneProvider
     */
    public function testValidRegexPhone(string $value): void
    {
        $constraint = new Regex(['pattern' => RegexUtils::PHONE_PATTERN]);
        $validator = $this->initValidator();

        $validator->validate($value, $constraint);
    }

    public function validPhoneProvider(): array
    {
        return [
            'concatenated number ' => ["0601020304"],
            'number with dial code without space' => ["+33601020304"],
        ];
    }

    /**
     * @dataProvider unvalidPhoneProvider
     */
    public function testUnvalidRegexPhone(string $value): void
    {
        $constraint = new Regex(['pattern' => RegexUtils::PHONE_PATTERN]);
        $validator = $this->initValidator(self::INVALID_MESSAGE);

        $validator->validate($value, $constraint);
    }

    public function unvalidPhoneProvider(): array
    {
        return [
            'number missing' => ["06 01 02 03 0"],
            'to much number' => ["06 01 02 03 04 5"],
            'number with space' => ["06 01 02 03 04"],
            'number with dash' => ["06-01-02-03-04"],
            'number with point' => ["06.01.02.03.04"],
            'number with separator combination' => ["0601 02-03.04"],
            'grouping of digits other than 2' => ["060 102 0304"],
            'missing + for dialing code' => ["33601020304"],
            'number with dial code with space' => ["+33 06 01 02 03 04"],
        ];
    }

    /**
     * @dataProvider validPostalCodeProvider
     */
    public function testValidRegexPostalCode(string $value): void
    {
        $constraint = new Regex(['pattern' => RegexUtils::POSTAL_CODE_PATTERN]);
        $validator = $this->initValidator();

        $validator->validate($value, $constraint);
    }

    public function validPostalCodeProvider(): array
    {
        return [
            'normal code' => ["26780"],
            'code with 0 at the beginning' => ["07400"],
            'code with first 0 optional' => ["7400"],
        ];
    }

    /**
     * @dataProvider unvalidPostalCodeProvider
     */
    public function testUnvalidRegexPostalCode(string $value): void
    {
        $constraint = new Regex(['pattern' => RegexUtils::POSTAL_CODE_PATTERN]);
        $validator = $this->initValidator(self::INVALID_MESSAGE);

        $validator->validate($value, $constraint);
    }

    public function unvalidPostalCodeProvider(): array
    {
        return [
            'number missing' => ["123"],
            'to much number' => ["123456"],
            'only 0' => ["00000"],
            'only 0 version 4 number' => ["0000"],
            'double 00 at the beginning and sequence of numbers' => ["00123"],
        ];
    }
}
