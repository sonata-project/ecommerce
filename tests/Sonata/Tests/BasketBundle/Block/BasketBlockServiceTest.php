<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\BasketBundle\Block;

use Sonata\BasketBundle\Block\BasketBlockService;

/**
 * Class BasketBlockServiceTest
 *
 * @package Sonata\Tests\BasketBundle
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BasketBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $engineInterfaceMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')->disableOriginalConstructor()->getMock();
        $customerManagerInterfaceMock = $this->getMockBuilder('Sonata\Component\Customer\CustomerManagerInterface')->disableOriginalConstructor()->getMock();
        $block = new BasketBlockService('test', $engineInterfaceMock);

        $this->assertEquals('Basket items', $block->getName());
    }
}
