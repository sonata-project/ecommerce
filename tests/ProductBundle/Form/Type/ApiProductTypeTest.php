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

namespace Sonata\ProductBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Form\Type\ApiProductType;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiProductTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $provider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getProvider')->will($this->returnValue($provider));

        $type = new ApiProductType($productPool);

        $builder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $type->buildForm($builder, ['provider_name' => 'test.product.provider']);
    }
}
