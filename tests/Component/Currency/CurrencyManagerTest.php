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

use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyManagerTest extends TestCase
{
    public function testFindOneByLabel(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $currencyManager = new CurrencyManager(Currency::class, $registry);

        $this->assertSame('EUR', $currencyManager->findOneByLabel('EUR')->getLabel());
    }
}
