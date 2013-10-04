<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\ProductBundle\Admin;

/**
 * Class GenerateProductCommandTest
 *
 * @package Sonata\Test\ProductBundle
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class CategoryAdminTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigureFormFields()
    {
        $categoryAdmin = $this->getMockBuilder('Sonata\ProductBundle\Admin\CategoryAdmin')->disableOriginalConstructor()->getMock();
        $formMapper = $this->getMockBuilder('Sonata\AdminBundle\Form\FormMapper')->disableOriginalConstructor()->getMock();
    }
}