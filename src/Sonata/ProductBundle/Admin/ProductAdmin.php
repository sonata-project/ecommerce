<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Admin;

use Sonata\BaseApplicationBundle\Admin\EntityAdmin;

class ProductAdmin extends EntityAdmin
{

    protected $class = 'Application\Sonata\ProductBundle\Entity\Product';
    protected $baseControllerName = 'SonataProductBundle:ProductAdmin';

    protected $list = array(
        'enabled',
        'name' => array('identifier' => true),
        'price',
        'stock',
    );

    protected $form = array(
        'name',
        'sku' => array('type' => 'string'),
        'description',
        'price',
        'vat',
        'stock',
        'image' => array('edit' => 'list')
    );

    protected $filter = array(
        'name',
//        'price',
        'enabled'
    );

}