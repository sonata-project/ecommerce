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

namespace Sonata\Component\Order;

use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Twig\Status\StatusClassRendererInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderStatusRenderer implements StatusClassRendererInterface
{
    public function handlesObject($object, $statusName = null)
    {
        return ($object instanceof OrderInterface || $object instanceof OrderElementInterface)
            && \in_array($statusName, ['delivery', 'payment', null], true);
    }

    public function getStatusClass($object, $statusName = null, $default = '')
    {
        switch ($statusName) {
            case 'delivery':
                switch ($object->getDeliveryStatus()) {
                    case BaseServiceDelivery::STATUS_COMPLETED:
                    case BaseServiceDelivery::STATUS_SENT:
                    case BaseServiceDelivery::STATUS_RETURNED:
                        return 'success';
                    case BaseServiceDelivery::STATUS_OPEN:
                    case BaseServiceDelivery::STATUS_PENDING:
                        return 'info';
                    default:
                        return $default;
                }

                break;
            case 'payment':
                switch ($object->getPaymentStatus()) {
                    case TransactionInterface::STATUS_OPEN:
                    case TransactionInterface::STATUS_VALIDATED:
                    case TransactionInterface::STATE_OK:
                        return 'success';
                    case TransactionInterface::STATUS_PENDING:
                        return 'info';
                    default:
                        return $default;
                }

                break;
            default:
                switch ($object->getStatus()) {
                    case OrderInterface::STATUS_OPEN:
                    case OrderInterface::STATUS_VALIDATED:
                        return 'success';
                    case OrderInterface::STATUS_PENDING:
                        return 'info';
                    default:
                        return $default;
                }

                break;
        }
    }
}
