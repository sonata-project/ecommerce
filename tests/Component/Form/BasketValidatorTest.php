<?php

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
use Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketValidatorTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testValidate()
    {
        $provider = $this->createMock(ProductProviderInterface::class);
        $provider->expects($this->once())->method('validateFormBasketElement');

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));

        $consValFact = $this->createMock(ConstraintValidatorFactory::class);

        $context = $this->createMock(ExecutionContext::class);
        $context->expects($this->once())->method('getViolations')->will($this->returnValue(['violation1']));
        $context->expects($this->once())->method('addViolationAt');

        $validator = new BasketValidator($pool, $consValFact);
        $validator->initialize($context);

        $elements = [$this->createMock(BasketElementInterface::class)];

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $constraint = new NotBlank();

        $validator->validate($basket, $constraint);
    }
}
