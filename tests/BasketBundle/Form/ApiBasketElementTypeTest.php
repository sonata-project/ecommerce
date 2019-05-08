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

namespace Sonata\BasketBundle\Tests\Form;

use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Form\ApiBasketElementType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketElementTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $type = new ApiBasketElementType('my.test.class');

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('create')->willReturnSelf();
        $builder->expects($this->once())->method('addModelTransformer');

        $type->buildForm($builder, []);
    }
}
