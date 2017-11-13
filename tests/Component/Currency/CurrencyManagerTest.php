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

use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\CurrencyManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyManagerTest extends TestCase
{
    public function testFindOneByLabel()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');

        $currencyManager = new CurrencyManager('Sonata\Component\Currency\Currency', $registry);

        $this->assertEquals('EUR', $currencyManager->findOneByLabel('EUR')->getLabel());
    }
}
