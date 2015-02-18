<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Tests\Form;

use Sonata\BasketBundle\Form\ApiBasketType;
use Sonata\Component\Currency\CurrencyDataTransformer;
use Sonata\Component\Currency\CurrencyFormType;

/**
 * Class ApiBasketTypeTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildForm()
    {
        $currencyManager = $this->getMock('Sonata\Component\Currency\CurrencyManagerInterface');

        $currencyDataTransformer = new CurrencyDataTransformer($currencyManager);

        $currencyFormType = new CurrencyFormType($currencyDataTransformer);

        $type = new ApiBasketType('my.test.class', $currencyFormType);

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();
        $builder->expects($this->once())->method('create')->will($this->returnSelf());

        $type->buildForm($builder, array());
    }
}
