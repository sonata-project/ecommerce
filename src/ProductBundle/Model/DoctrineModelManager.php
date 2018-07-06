<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Model;

use Sonata\Component\Product\Pool;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * this method overwrite the default AdminModelManager to call
 * the custom methods from the dedicated media manager.
 */
class DoctrineModelManager extends ModelManager
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @param RegistryInterface $registry
     * @param Pool              $pool
     */
    public function __construct(RegistryInterface $registry, Pool $pool)
    {
        parent::__construct($registry);

        $this->pool = $pool;
    }

    public function create($object): void
    {
        $this->pool->getManager($object)->save($object);
    }

    public function update($object): void
    {
        $this->pool->getManager($object)->save($object);
    }

    public function delete($object): void
    {
        $this->pool->getManager($object)->delete($object);
    }
}
