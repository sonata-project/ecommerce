<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Component\Invoice;

use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Status\StatusClassRendererInterface;


/**
 * Class InvoiceStatusRenderer
 *
 * @package Sonata\Component\Invoice
 *
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class InvoiceStatusRenderer implements StatusClassRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function handlesObject($object, $statusType = null)
    {
        return $object instanceof InvoiceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusClass($object, $statusType = null, $default = "")
    {
        if (!$this->handlesObject($object, $statusType)) {
            return $default;
        }

        switch ($object->getStatus()) {
            case InvoiceInterface::STATUS_CONFLICT:
                return 'important';
            case InvoiceInterface::STATUS_OPEN:
                return 'warning';
            case InvoiceInterface::STATUS_PAID:
                return 'success';
        }

        return $default;
    }
}
