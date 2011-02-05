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

class CustomerAdmin extends EntityAdmin
{

    protected $class = 'Application\Sonata\CustomerBundle\Entity\Customer';
    protected $baseControllerName = 'SonataCustomerBundle:CustomerAdmin';
    
    protected $form = array(
        'firstname',
        'lastname'
    );

    protected $list = array(
        'name' => array('code' => '__toString', 'identifier' => true, 'type' => 'string'),
        'createdAt'
    );

    protected $filter = array(
        'name' => array('type' => 'string'),
//        'createdAt'
    );


}