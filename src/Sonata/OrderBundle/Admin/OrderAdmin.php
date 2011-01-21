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

use Sonata\BaseApplicationBundle\Admin\EntityAdmin;

class OrderAdmin extends EntityAdmin
{

    protected $class = 'Application\Sonata\OrderBundle\Entity\Order';


    protected $baseControllerName = 'SonataOrderBundle:OrderAdmin';
    
}