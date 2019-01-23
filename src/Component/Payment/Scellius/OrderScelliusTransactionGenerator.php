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

namespace Sonata\Component\Payment\Scellius;

use Sonata\Component\Order\OrderInterface;

/**
 * This method returns the sequence number from the Order reference (only works if the generator is the MysqlReference)
 *   => ie: YYMMDDXXXXXX.
 */
class OrderScelliusTransactionGenerator implements ScelliusTransactionGeneratorInterface
{
    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function generate(OrderInterface $order)
    {
        if (12 !== \strlen($order->getReference())) {
            throw new \RuntimeException('Invalid reference length');
        }

        return substr($order->getReference(), -6);
    }
}
