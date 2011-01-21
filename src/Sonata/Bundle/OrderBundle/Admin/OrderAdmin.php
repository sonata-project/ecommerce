<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\OrderBundle\Admin;

use Bundle\Sonata\BaseApplicationBundle\Admin\EntityAdmin;

class OrderAdmin extends EntityAdmin
{

    protected $class = 'Application\OrderBundle\Entity\Order';

    protected $baseRouteName = 'sonata_admin_order';
    protected $baseRoutePattern = '/admin/sonata/order';

//    protected $baseControllerName = 'Sonata\Bundle\OrderBundle:OrderAdmin';
    
}