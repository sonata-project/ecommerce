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
use Knplabs\MenuBundle\Menu;
use Knplabs\MenuBundle\MenuItem;
    
abstract class BaseProductAdmin extends EntityAdmin
{
    
    /**
     * return the edit template
     *
     * @return string the edit template
     */
    public function getEditTemplate()
    {
        return 'SonataProductBundle:ProductAdmin:edit.html.twig';
    }

    /**
     * return the edit template
     *
     * @return string the edit template
     */
    public function getListTemplate()
    {
        return 'SonataProductBundle:ProductAdmin:list.html.twig';
    }

    public function configureUrls()
    {

        $admin = $this->isChild() ? $this->getParent() : $this;
        
        $this->urls['duplicate'] = array(
            'name'      => $admin->getBaseRouteName().'_duplicate',
            'pattern'   => $admin->getBaseRoutePattern().'/{id}/duplicate',
        );

        $this->urls['variation_list'] = array(
            'name'      => $admin->getBaseRouteName().'_variation_list',
            'pattern'   => $admin->getBaseRoutePattern().'/{id}/variation-list',
        );

        $this->urls['category'] = array(
            'name'      => $admin->getBaseRouteName().'_category',
            'pattern'   => $admin->getBaseRoutePattern().'/{id}/category',
        );

    }

}