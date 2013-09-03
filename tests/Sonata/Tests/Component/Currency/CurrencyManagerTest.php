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
    /**
     * @var CurrencyManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CurrencyManager();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Sonata\Component\Currency\CurrencyManager::findOneByLabel
     */
    public function testFindOneByLabel()
    {
        $this->assertEquals("EUR", $this->object->findOneByLabel("EUR")->getLabel());
    }
}
?>
