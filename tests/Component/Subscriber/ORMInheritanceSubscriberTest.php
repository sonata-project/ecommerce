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

use App\Sonata\ProductBundle\Entity\Product;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Subscriber\ORMInheritanceSubscriber;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class ORMInheritanceSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscriber = new ORMInheritanceSubscriber([], Product::class);
        static::assertCount(1, $subscriber->getSubscribedEvents());
    }

    public function testLoadClassMetadata(): void
    {
        $fakedMetadata = new \stdClass();
        $fakedMetadata->name = 'IncorrectValue';

        $subscriber = new ORMInheritanceSubscriber([], Product::class);
        $metadata = $this->createMock(LoadClassMetadataEventArgs::class);
        $metadata->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($fakedMetadata);

        static::assertNull($subscriber->loadClassMetadata($metadata));
        unset($fakedMetadata);

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = Product::class;
        $metadata = $this->createMock(LoadClassMetadataEventArgs::class);
        $metadata->expects(static::any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        try {
            $subscriber->loadClassMetadata($metadata);
        } catch (\Exception $e) {
            static::fail('->loadClassMetadata() should not throw an exception when using Product entity');
        }
    }
}
