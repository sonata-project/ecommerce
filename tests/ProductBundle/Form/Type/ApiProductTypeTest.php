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
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\ProductBundle\Form\Type\ApiProductType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiProductTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $provider = $this->createMock(ProductProviderInterface::class);

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProvider')->willReturn($provider);

        $type = new ApiProductType($productPool);

        $builder = $this->createMock(FormBuilder::class);

        $type->buildForm($builder, ['provider_name' => 'test.product.provider']);
    }
}
