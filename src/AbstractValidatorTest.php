<?php

namespace Smart\CoreBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * MDT Rewrite of AbstractValidatorTest from standard-bundle but with php 8.4 support.
 * To move on standard-bundle when full 8.4 support is ok.
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
abstract class AbstractValidatorTest extends TestCase
{
    /**
     * @return object The validator instance
     */
    abstract protected function getValidatorInstance();

    /**
     * @param string|null $expectedMessage
     * @return object
     */
    protected function initValidator(?string $expectedMessage = null)
    {
        $validator = $this->getValidatorInstance();

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);

        if ($expectedMessage !== null) {
            // Configuration pour les tests qui attendent une violation
            // Retour du même builder après chaque méthode pour permettre le chaînage
            $builder->method('addViolation')
                ->willReturn($builder);

            $builder->method('setParameter')
                ->willReturn($builder);

            $builder->method('setCode')
                ->willReturn($builder);

            // Maintenant, définissons les attentes
            $builder->expects($this->once())
                ->method('addViolation');

            // Dans les validateurs d'email et certains autres, setParameter est
            // appelé avec le motif et la valeur
            $builder->expects($this->any())
                ->method('setParameter');
        } else {
            // Pour les tests qui n'attendent pas de violation
            $builder->expects($this->never())
                ->method('addViolation');
        }

        $context = $this->createMock(ExecutionContextInterface::class);

        if ($expectedMessage !== null) {
            // Pour les tests qui attendent une violation
            $context->expects($this->once())
                ->method('buildViolation')
                ->with($expectedMessage)
                ->willReturn($builder);
        } else {
            // Pour les tests qui n'attendent pas de violation
            $context->expects($this->never())
                ->method('buildViolation');
        }

        $validator->initialize($context); // @phpstan-ignore-line

        return $validator;
    }
}
