<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Form\Type;

use Sonata\CoreBundle\Tests\Form\Type\DoctrineORMSerializationTypeTest;
use Sonata\ProductBundle\Form\Type\ApiProductType;

/**
 * Class ApiProductTypeTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiProductTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildForm()
    {
        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');

        $productPool = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $productPool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));

        $type = new ApiProductType($productPool);

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $type->buildForm($builder, array("provider_name" => "test.product.provider"));
    }
}