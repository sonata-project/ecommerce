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

class AddressAdmin extends EntityAdmin
{

    protected $class = 'Application\Sonata\CustomerBundle\Entity\Address';

    protected $formFields = array(
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
        'customer',
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

    protected $listFields = array(
        'fulladdress' => array('code' => 'getFullAddress', 'identifier' => true),
        'name',
        'current',
        'typeCode',
        'customer'
    );

    protected $baseControllerName = 'SonataCustomerBundle:AddressAdmin';

    public function configureFormFields()
    {
        $this->formFields['type']->setType('choice');
        $this->formFields['type']->mergeOption('form_field_options', array(
            'choices' => \Application\Sonata\CustomerBundle\Entity\Address::getTypesList()
        ));
    }
}