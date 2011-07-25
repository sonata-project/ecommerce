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

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class OrderAdmin extends Admin
{
    protected $parentAssociationMapping = 'customer';

    protected $list = array(
        'id' => array('identifier' => true),
        'reference' => array('identifier' => true),
        'customer',
        'status',
        'paymentStatus',
        'validatedAt',
        'totalExcl'
    );

    protected $filter = array(
        'reference',
//        'customer'
    );

    protected $form = array(
        'currency',
        'status',
        'paymentStatus',
        'deliveryStatus',
        'validatedAt',
        'billingName',
        'billingAddress1',
        'billingAddress2',
        'billingAddress3',
        'billingCity',
        'billingPostcode',
        'billingCountryCode' => array('type' => 'country'),
        'billingFax',
        'billingEmail',
        'billingMobile',
        'shippingName',
        'shippingAddress1',
        'shippingAddress2',
        'shippingAddress3',
        'shippingCity',
        'shippingPostcode',
        'shippingCountryCode' => array('type' => 'country'),
        'shippingFax',
        'shippingEmail',
        'shippingMobile',
        'orderElements' => array('edit' => 'inline', 'inline' => 'table'),
//        'customer' => array('edit' => 'list')
    );


    public function configureFormFields(FormMapper $form)
    {
        if (!$this->isChild()) {
            $form->add('customer', array(), array('edit' => 'list'));
        }
    }
}