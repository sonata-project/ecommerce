<?php

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
use Sonata\BasketBundle\Form\ApiBasketType;
use Sonata\Component\Currency\CurrencyDataTransformer;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Currency\CurrencyManagerInterface;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketTypeTest extends TestCase
{
    public function testBuildForm()
    {
        $currencyManager = $this->createMock(CurrencyManagerInterface::class);

        $currencyDataTransformer = new CurrencyDataTransformer($currencyManager);

        $currencyFormType = new CurrencyFormType($currencyDataTransformer);

        $type = new ApiBasketType('my.test.class', $currencyFormType);

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('create')->will($this->returnSelf());

        $type->buildForm($builder, []);
    }
}
