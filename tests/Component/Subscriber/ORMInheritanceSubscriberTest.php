<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\Component\Subscriber;

use Sonata\Component\Subscriber\ORMInheritanceSubscriber;

/**
 * Class ORMInheritanceSubscriberTest
 *
 * @package Sonata\Test\Component
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class ORMInheritanceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSubscribedEvents()
    {
        $subscriber = new ORMInheritanceSubscriber(array());
        $this->assertCount(1, $subscriber->getSubscribedEvents());
    }

    public function testLoadClassMetadata()
    {
        $fakedMetadata = new \stdClass();
        $fakedMetadata->name = 'IncorrectValue';

        $subscriber = new ORMInheritanceSubscriber(array());
        $metadata = $this->getMockBuilder('Doctrine\ORM\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $metadata->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($fakedMetadata));

        $this->assertNull($subscriber->loadClassMetadata($metadata));
        unset($fakedMetadata);

        $classMetadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()->getMock();
        $classMetadata->name = 'Application\Sonata\ProductBundle\Entity\Product';
        $metadata = $this->getMockBuilder('Doctrine\ORM\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $metadata->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($classMetadata));

        try {
            $subscriber->loadClassMetadata($metadata);
        } catch (\Exception $e) {
            $this->fail('->loadClassMetadata() should not throw an exception when using Product entity');
        }
    }
}
