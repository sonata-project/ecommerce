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
 * This method returns none, so the request binary will generates one for use.
 */
class NodeScelliusTransactionGenerator implements ScelliusTransactionGeneratorInterface
{
    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     *
     * @return string
     */
    public function generate(OrderInterface $order)
    {
        return '';
    }
}
