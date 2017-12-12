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

namespace Sonata\Component\Tests\Subscriber;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Subscriber\ORMInheritanceSubscriber;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class ORMInheritanceSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscriber = new ORMInheritanceSubscriber([], 'Application\Sonata\ProductBundle\Entity\Product');
        $this->assertCount(1, $subscriber->getSubscribedEvents());
    }

    public function testLoadClassMetadata(): void
    {
        $fakedMetadata = new \stdClass();
        $fakedMetadata->name = 'IncorrectValue';

        $subscriber = new ORMInheritanceSubscriber([], 'Application\Sonata\ProductBundle\Entity\Product');
        $metadata = $this->createMock('Doctrine\ORM\Event\LoadClassMetadataEventArgs');
        $metadata->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($fakedMetadata));

        $this->assertNull($subscriber->loadClassMetadata($metadata));
        unset($fakedMetadata);

        $classMetadata = $this->createMock('Doctrine\ORM\Mapping\ClassMetadata');
        $classMetadata->name = 'Application\Sonata\ProductBundle\Entity\Product';
        $metadata = $this->createMock('Doctrine\ORM\Event\LoadClassMetadataEventArgs');
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
