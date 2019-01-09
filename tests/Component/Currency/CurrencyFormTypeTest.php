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

namespace Sonata\Component\Tests\Currency;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\CurrencyDataTransformer;
use Sonata\Component\Currency\CurrencyFormType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyFormTypeTest extends TestCase
{
    /**
     * @var CurrencyFormType
     */
    protected $currencyFormType;

    public function setUp(): void
    {
        $currencyDataTransformer = $this->createMock(CurrencyDataTransformer::class);
        $this->currencyFormType = new CurrencyFormType($currencyDataTransformer);
    }

    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('addModelTransformer');

        $this->currencyFormType->buildForm($formBuilder, []);
    }

    public function testGetParent(): void
    {
        $this->assertEquals('currency', $this->currencyFormType->getParent());
    }

    public function testGetName(): void
    {
        $this->assertEquals('sonata_currency', $this->currencyFormType->getName());
    }
}
