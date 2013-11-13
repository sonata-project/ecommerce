<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\CustomerBundle\Block;

use Sonata\CustomerBundle\Block\RecentCustomersBlockService;

/**
 * Class RecentCustomersBlockServiceTest
 *
 * @package Sonata\Test\CustomerBundle
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class RecentCustomersBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $engineInterfaceMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')->disableOriginalConstructor()->getMock();
        $customerManagerInterfaceMock = $this->getMockBuilder('Sonata\Component\Customer\CustomerManagerInterface')->disableOriginalConstructor()->getMock();
        $block = new RecentCustomersBlockService('test', $engineInterfaceMock, $customerManagerInterfaceMock);

        $this->assertEquals('Recent Customers', $block->getName());
    }
}
