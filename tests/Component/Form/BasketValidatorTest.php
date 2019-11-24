<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Form;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Form\BasketValidator;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductProviderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketValidatorTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testValidate(): void
    {
        $provider = $this->createMock(ProductProviderInterface::class);
        $provider->expects($this->once())->method('validateFormBasketElement');

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())->method('getProvider')->willReturn($provider);

        $consValFact = $this->createMock(ContainerConstraintValidatorFactory::class);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $context = $this->createMock(ExecutionContext::class);
        $context->expects($this->once())
                ->method('getViolations')
                ->willReturn(new ConstraintViolationList([
                    $this->createMock(ConstraintViolationInterface::class),
                ]));
        $context->expects($this->once())->method('buildViolation')->willReturn($violationBuilder);

        $validator = new BasketValidator($pool, $consValFact);
        $validator->initialize($context);

        $elements = [$this->createMock(BasketElementInterface::class)];

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getBasketElements')->willReturn($elements);

        $constraint = new NotBlank();

        $validator->validate($basket, $constraint);
    }
}
