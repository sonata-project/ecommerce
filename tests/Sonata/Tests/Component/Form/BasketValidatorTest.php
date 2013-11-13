<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Form;

use Sonata\Component\Form\BasketValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class BasketValidatorTest
 *
 * @package Sonata\Tests\Component\Form
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $provider->expects($this->once())->method('validateFormBasketElement');

        $pool = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $pool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));

        $consValFact = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory')->disableOriginalConstructor()->getMock();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')->disableOriginalConstructor()->getMock();
        $context->expects($this->once())->method('getViolations')->will($this->returnValue(array('violation1')));
        $context->expects($this->once())->method('addViolationAt');

        $validator = new BasketValidator($pool, $consValFact);
        $validator->initialize($context);

        $elements = array($this->getMock('Sonata\Component\Basket\BasketElementInterface'));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $constraint = new NotBlank();

        $validator->validate($basket, $constraint);
    }
}
