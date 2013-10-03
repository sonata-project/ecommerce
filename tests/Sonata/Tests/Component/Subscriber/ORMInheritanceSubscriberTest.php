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
        $this->assertInternalType('array', $subscriber->getSubscribedEvents());
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

        $metadata->name = 'Application\Sonata\ProductBundle\Entity\Product';
        try {
            $subscriber->loadClassMetadata($metadata);
        }
        catch (\Exception $e) {
            $this->fail('->loadClassMetadata() should not throw an exception when using Product entity');
        }
    }
}