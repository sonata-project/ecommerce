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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class CategoryAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled')
            ->add('name')
            ->add('description')
            ->add('subDescription')
            ->add('position')
        ;
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('enabled')
            ->add('position')
            ->add('updatedAt')
            ->add('createdAt')
        ;
    }
}