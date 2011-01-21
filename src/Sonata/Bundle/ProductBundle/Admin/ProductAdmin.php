<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Admin;

use Bundle\Sonata\BaseApplicationBundle\Admin\EntityAdmin;

class ProductAdmin extends EntityAdmin
{

    protected $class = 'Application\ProductBundle\Entity\Product';

    protected $listFields = array(
        'enabled',
        'name' => array('identifier' => true),
        'price',
        'stock',
    );

    protected $formFields = array(
        'name',
        'sku' => array('type' => 'string'),
        'description',
        'price',
        'vat',
        'stock',
        'image'
    );

    protected $baseRouteName = 'sonata_admin_product';
    protected $baseRoutePattern = '/admin/sonata/product';


}