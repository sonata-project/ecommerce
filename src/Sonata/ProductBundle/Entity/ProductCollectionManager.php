<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\Pool;

use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;
use Sonata\AdminBundle\Datagrid\ORM\Pager;

use Doctrine\ORM\EntityManager;

class ProductCollectionManager extends ProductManager
{
    /**
     * Deletes a product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return void
     */
    public function delete(ProductInterface $product)
    {
        throw new \RuntimeException('A ProductCollectionManager cannot delete a product');
    }

    /**
     * Creates an empty medie instance
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function create()
    {
        throw new \RuntimeException('A ProductCollectionManager cannot create a product');
    }

    /**
     * Creates an empty medie instance
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function save(ProductInterface $product)
    {
        throw new \RuntimeException('A ProductCollectionManager cannot save a product');
    }
}