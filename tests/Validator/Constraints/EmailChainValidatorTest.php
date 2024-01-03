<?php

namespace Smart\CoreBundle\Tests\Validator\Constraints;

use Smart\CoreBundle\Validator\Constraints\EmailChain;
use Smart\CoreBundle\Validator\Constraints\EmailChainValidator;
use Smart\StandardBundle\Validator\Constraints\AbstractValidatorTest;

/**
 * vendor/bin/simple-phpunit tests/Validator/Constraints/EmailChainValidatorTest.php
 */
class EmailChainValidatorTest extends AbstractValidatorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new EmailChainValidator();
    }

    /**
     * @dataProvider failProvider
     */
    public function testValidationFail(string $value): void
    {
        $constraint = new EmailChain();
        $validator = $this->initValidator('email_chain.format_error');

        $validator->validate($value, $constraint);
    }

    public function failProvider(): array
    {
        return [
            'extension missing' => ["missing@extension"],
            'at missing' => ["missing_arobase.fr"],
            'first error and second fail' => ["first@ok.fr,second@fail"],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testValidationOk(string $value): void
    {
        $constraint = new EmailChain();
        $validator = $this->initValidator();

        $validator->validate($value, $constraint);
    }

    public function validProvider(): array
    {
        return [
            'simple valid email' => ["test@valid.fr"],
            'multiple valid email' => ["test1@valid.fr,test2@valid.fr"],
            'multiple valid email with space' => ["  test1@valid.fr  ,  test2@valid.fr  "],
        ];
    }
}
