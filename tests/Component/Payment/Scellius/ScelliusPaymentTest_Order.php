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

namespace Sonata\Component\Tests\Payment\Scellius;

use Sonata\Component\Currency\Currency;
use Sonata\OrderBundle\Entity\BaseOrder;

class ScelliusPaymentTest_Order extends BaseOrder
{
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int the order id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCurrency()
    {
        if (null === $this->currency) {
            return new Currency();
        }

        return $this->currency;
    }
}
