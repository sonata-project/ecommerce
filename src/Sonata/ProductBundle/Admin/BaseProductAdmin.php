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

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;

use Knplabs\Bundle\MenuBundle\Menu;
use Knplabs\Bundle\MenuBundle\MenuItem;
    
abstract class BaseProductAdmin extends Admin
{
    
    /**
     * return the edit template
     *
     * @return string the edit template
     */
    public function getEditTemplate()
    {
        return 'SonataProduct:ProductAdmin:edit.html.twig';
    }

    /**
     * return the edit template
     *
     * @return string the edit template
     */
    public function getListTemplate()
    {
        return 'SonataProduct:ProductAdmin:list.html.twig';
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $admin = $this->isChild() ? $this->getParent() : $this;
        
        $collection->add('duplicate', $admin->getRouterIdParameter().'/duplicate');
        $collection->add('variation_list', $admin->getRouterIdParameter().'/variation-list');
        $collection->add('category', $admin->getRouterIdParameter().'/category');
    }
}