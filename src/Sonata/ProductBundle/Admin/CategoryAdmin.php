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
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;

class CategoryAdmin extends Admin
{
    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('name')
            ->add('descriptionFormatter', 'sonata_formatter_type_selector', array(
                'source' => 'rawDescription',
                'target' => 'description'
            ))
            ->add('rawDescription', null, array('attr' => array('class' => 'span10', 'rows' => 20)))
            ->add('subDescription')
            ->add('position')
        ;

        if ($this == $this->getRoot()) {
            $formMapper
                ->add('children', 'sonata_type_collection', array(
                    'by_reference' => false
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position'
                ))
            ;
        }
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
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

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $formatterPool
     *
     * @return void
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * @return \Sonata\FormatterBundle\Formatter\Pool
     */
    public function getPoolFormatter()
    {
        return $this->formatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($category)
    {
        $category->setDescription($this->getPoolFormatter()->transform($category->getDescriptionFormatter(), $category->getRawDescription()));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($category)
    {
        $category->setDescription($this->getPoolFormatter()->transform($category->getDescriptionFormatter(), $category->getRawDescription()));
    }
}