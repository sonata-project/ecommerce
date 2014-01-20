<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\PaymentBundle\Entity;

use Sonata\PaymentBundle\Entity\BaseTransaction;
use Sonata\Component\Order\OrderInterface;

class Transaction extends BaseTransaction
{
    public function getId()
    {
        return 1;
    }
}

class BaseTransactionTest extends \PHPUnit_Framework_TestCase
{
    public function testInformation()
    {
        $transaction = new Transaction();

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getId')->will($this->returnValue(123));
        $order->expects($this->once())->method('getReference')->will($this->returnValue('B00120'));

        $transaction->setOrder($order);
        $transaction->setTransactionId('XCADC');
        $transaction->setState(Transaction::STATE_KO);
        $transaction->setStatusCode(Transaction::STATUS_VALIDATED);

        $expected =<<<INFO
Transaction created
Update status code to `0` (open)
The transaction is linked to the Order : id = `123` / Reference = `B00120`
The transactionId is `XCADC`
The transaction state is `UNKNOWN`
Update status code to `2` (validated)
INFO;

        $this->assertEquals($expected, $transaction->getInformation());
    }

    public function testParametersEncoding()
    {
        $transaction = new Transaction();

        $inParams = array('params' => array(
            "aerẑerüioRazeioj" => iconv('UTF-8', 'ISO-8859-1', "ôûêîÖüïë"),
            "abcdef" => "ghijkl"
        ));

        $expectedParams = array('params' => array(
            "aerẑerüioRazeioj" => "ôûêîÖüïë",
            "abcdef" => "ghijkl"
        ));

        $transaction->setParameters($inParams);
        $this->assertEquals($expectedParams, $transaction->getParameters());
    }
}
