<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\DeliveryManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

class DeliveryManager extends BaseEntityManager implements DeliveryManagerInterface
{
}
