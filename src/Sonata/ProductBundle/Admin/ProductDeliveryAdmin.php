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

class ProductDeliveryAdmin extends BaseProductAdmin
{

    protected $parentAssociationMapping = 'product';
    
    protected $list = array(
        'id' => array('identifier' => true),
        'perItem',
        'countryCode',
        'zone',
        'enabled'
    );

    protected $form = array(
        'perItem',
        'countryCode',
        'zone',
        'enabled',
        'code',
//        'product'
    );

}