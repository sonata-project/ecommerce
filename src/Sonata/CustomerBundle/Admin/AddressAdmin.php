<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Admin;

use Sonata\BaseApplicationBundle\Admin\EntityAdmin;
use Sonata\BaseApplicationBundle\Form\FormMapper;
use Sonata\BaseApplicationBundle\Datagrid\DatagridMapper;
use Sonata\BaseApplicationBundle\Datagrid\ListMapper;

use Application\Sonata\CustomerBundle\Entity\Address;

class AddressAdmin extends EntityAdmin
{

    protected $class = 'Application\Sonata\CustomerBundle\Entity\Address';
    protected $baseControllerName = 'SonataCustomerBundle:AddressAdmin';

    protected $form = array(
        'firstname',
        'lastname',
        'name',
        'city',
        'address1',
        'address2',
        'address3',
        'postcode',
        'phone',
        'countryCode' => array('type' => 'country'),
        'customer'  => array('edit' => 'list'),
        'type',
        'current'
    );

    protected $formGroups = array(
        'Advanced' => array(
            'fields' => array('type', 'current', 'name')
        ),
        'Contact' => array(
            'fields' => array('customer', 'firstname', 'lastname', 'phone')
        ),
        'Address' => array(
            'fields' => array('address1', 'address2', 'address3', 'postcode', 'city','countryCode')
        ),

    );

    protected $list = array(
        'fulladdress' => array('code' => 'getFullAddress', 'identifier' => true, 'type' => 'string'),
        'name',
        'current',
        'typeCode' => array('type' => 'string'),
        'customer'
    );

    public function configureFormFields(FormMapper $form)
    {
        $form->add('type', array('choices' => Address::getTypesList()), array('type' => 'choice'));
    }

    public function configureDatagridFilters(DatagridMapper $datagrid)
    {

    }

    public function configureListFields(ListMapper $list)
    {

    }
}