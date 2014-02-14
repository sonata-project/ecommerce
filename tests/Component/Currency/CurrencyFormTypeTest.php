<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\Component\Currency\Types;

use Sonata\Component\Currency\CurrencyFormType;

/**
 * Class CurrencyFormTypeTest
 *
 * @package Sonata\Test\Component\Currency\Types
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyFormTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyFormType
     */
    protected $currencyFormType;

    public function setUp()
    {
        $currencyDataTransformer = $this->getMockBuilder('Sonata\Component\Currency\CurrencyDataTransformer')->disableOriginalConstructor()->getMock();
        $this->currencyFormType = new CurrencyFormType($currencyDataTransformer);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();
        $formBuilder->expects($this->once())
            ->method('addModelTransformer');

        $this->currencyFormType->buildForm($formBuilder, array());
    }

    public function testGetParent()
    {
        $this->assertEquals('currency', $this->currencyFormType->getParent());
    }

    public function testGetName()
    {
        $this->assertEquals('sonata_currency', $this->currencyFormType->getName());
    }
}
