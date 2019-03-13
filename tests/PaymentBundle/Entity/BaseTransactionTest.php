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

namespace Sonata\PaymentBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderInterface;
use Sonata\PaymentBundle\Entity\BaseTransaction;

class Transaction extends BaseTransaction
{
    public function getId()
    {
        return 1;
    }
}

class BaseTransactionTest extends TestCase
{
    public function testInformation(): void
    {
        $transaction = new Transaction();

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getId')->will($this->returnValue(123));
        $order->expects($this->once())->method('getReference')->will($this->returnValue('B00120'));

        $transaction->setOrder($order);
        $transaction->setTransactionId('XCADC');
        $transaction->setState(Transaction::STATE_KO);
        $transaction->setStatusCode(Transaction::STATUS_VALIDATED);

        $expected = <<<'INFO'
Transaction created
Update status code to `0` (open)
The transaction is linked to the Order : id = `123` / Reference = `B00120`
The transactionId is `XCADC`
The transaction state is `UNKNOWN`
Update status code to `2` (validated)
INFO;

        $this->assertSame($expected, $transaction->getInformation());
    }

    public function testParametersEncoding(): void
    {
        $transaction = new Transaction();

        $inParams = ['params' => [
            'aerẑerüioRazeioj' => iconv('UTF-8', 'ISO-8859-1', 'ôûêîÖüïë'),
            'abcdef' => 'ghijkl',
        ]];

        $expectedParams = ['params' => [
            'aerẑerüioRazeioj' => 'ôûêîÖüïë',
            'abcdef' => 'ghijkl',
        ]];

        $transaction->setParameters($inParams);
        $this->assertSame($expectedParams, $transaction->getParameters());
    }
}
