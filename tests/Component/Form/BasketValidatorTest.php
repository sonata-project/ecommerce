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
use Sonata\Component\Form\BasketValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $provider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');
        $provider->expects($this->once())->method('validateFormBasketElement');

        $pool = $this->createMock('Sonata\Component\Product\Pool');
        $pool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));

        $consValFact = $this->createMock('Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory');

        $context = $this->createMock('Symfony\Component\Validator\ExecutionContext');
        $context->expects($this->once())->method('getViolations')->will($this->returnValue(['violation1']));
        $context->expects($this->once())->method('addViolationAt');

        $validator = new BasketValidator($pool, $consValFact);
        $validator->initialize($context);

        $elements = [$this->createMock('Sonata\Component\Basket\BasketElementInterface')];

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $constraint = new NotBlank();

        $validator->validate($basket, $constraint);
    }
}
