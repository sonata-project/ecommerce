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

namespace Sonata\Component\Tests\Generator;

use Sonata\InvoiceBundle\Entity\BaseInvoice;

class InvoiceMock extends BaseInvoice
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}
