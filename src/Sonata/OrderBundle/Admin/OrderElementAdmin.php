<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Admin;

use Sonata\AdminBundle\Admin\EntityAdmin;

class OrderElementAdmin extends EntityAdmin
{

    protected $parent = 'order';

    protected $form = array(
//        'product',
        'productType',
        'quantity',
        'price',
        'vat',
        'designation',
        'description',
        'status',
        'deliveryStatus'
    );
}