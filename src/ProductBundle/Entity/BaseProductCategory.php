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

namespace Sonata\ProductBundle\Entity;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductInterface;

abstract class BaseProductCategory implements ProductCategoryInterface
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var bool
     */
    protected $main;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var CategoryInterface
     */
    protected $category;

    public function __toString()
    {
        return ($this->getProduct() ? $this->getProduct()->getName() : 'null').' - '.($this->getCategory() ? $this->getCategory()->getName() : 'null');
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setMain($main): void
    {
        $this->main = $main;
    }

    public function getMain()
    {
        return $this->main;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setCategory(CategoryInterface $category): void
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }
}
