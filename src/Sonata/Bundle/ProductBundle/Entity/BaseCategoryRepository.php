<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Entity;

class BaseCategoryRepository extends \Doctrine\ORM\EntityRepository
{

    protected $categories = null;

    public function getRootCategory()
    {
        $this->loadCategories();

        return $this->categories[0];
    }

    public function loadCategories()
    {

        if($this->categories !== null) {
            
            return;
        }

        $class = $this->getEntityName();


        $this->categories = $this
            ->getEntityManager()
            ->createQuery('SELECT c FROM Application\ProductBundle\Entity\Category c INDEX BY c.id')
            ->execute();

        $root = new $class;
        $root->setName('root');

        foreach( $this->categories as $category) {

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();
            
            if(!$parent) {
                $root->addChildren($category);

                continue;
            }

            $parent->addChildren($category);
        }

        $this->categories[0] = $root;
    }
}