<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Currency;

use Sonata\Component\Currency\CurrencyManager;

/**
 * Test class for CurrencyManager.
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testFindOneByLabel()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');

        $currencyManager = new CurrencyManager('Sonata\Component\Currency\Currency', $registry);

        $this->assertEquals("EUR", $currencyManager->findOneByLabel("EUR")->getLabel());
    }
}
