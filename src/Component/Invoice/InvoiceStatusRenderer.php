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

namespace Sonata\Component\Invoice;

use Sonata\Twig\Status\StatusClassRendererInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class InvoiceStatusRenderer implements StatusClassRendererInterface
{
    public function handlesObject($object, $statusName = null)
    {
        return $object instanceof InvoiceInterface;
    }

    public function getStatusClass($object, $statusName = null, $default = '')
    {
        switch ($object->getStatus()) {
            case InvoiceInterface::STATUS_CONFLICT:
                return 'danger';
            case InvoiceInterface::STATUS_OPEN:
                return 'warning';
            case InvoiceInterface::STATUS_PAID:
                return 'success';
        }

        return $default;
    }
}
