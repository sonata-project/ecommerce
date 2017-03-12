<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Currency;

use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyFormTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyFormType
     */
    protected $currencyFormType;

    public function setUp()
    {
        $currencyDataTransformer = $this->createMock('Sonata\Component\Currency\CurrencyDataTransformer');
        $this->currencyFormType = new CurrencyFormType($currencyDataTransformer);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilder');
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
