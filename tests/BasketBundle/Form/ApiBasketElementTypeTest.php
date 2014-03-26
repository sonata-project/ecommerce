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

use Sonata\BasketBundle\Form\ApiBasketElementType;

/**
 * Class ApiBasketElementTypeTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketElementTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildForm()
    {
        $type = new ApiBasketElementType('my.test.class');

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();
        $builder->expects($this->once())->method('create')->will($this->returnSelf());
        $builder->expects($this->once())->method('addModelTransformer');

        $type->buildForm($builder, array());
    }
}