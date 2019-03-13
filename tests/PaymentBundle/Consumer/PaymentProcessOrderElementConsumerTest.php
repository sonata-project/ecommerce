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

namespace Sonata\PaymentBundle\Tests\Consumer;

use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Tests\Product\OrderElement;
use Sonata\OrderBundle\Entity\OrderElementManager;
use Sonata\PaymentBundle\Consumer\PaymentProcessOrderElementConsumer;

class PaymentProcessOrderElementConsumerTest extends TestCase
{
    public function testGenerateDiffValue(): void
    {
        $consumer = $this->getConsumer();

        $this->assertSame(-1, $consumer->generateDiffValue(TransactionInterface::STATUS_VALIDATED, OrderInterface::STATUS_VALIDATED, 1));
        $this->assertSame(-10, $consumer->generateDiffValue(TransactionInterface::STATUS_VALIDATED, OrderInterface::STATUS_VALIDATED, 10));
    }

    /**
     * @return PaymentProcessOrderElementConsumer
     */
    protected function getConsumer()
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $orderElementManager = new OrderElementManager(OrderElement::class, $registry);

        $pool = new Pool();

        $consumer = new PaymentProcessOrderElementConsumer($orderElementManager, $pool);

        return $consumer;
    }
}
