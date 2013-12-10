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

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductInterface;

/**
 * Sonata\ProductBundle\Entity\BaseProductCategory
 */
abstract class BaseProductCategory implements ProductCategoryInterface
{
    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var boolean $main
     */
    protected $main;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
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
        return ($this->getProduct() ? $this->getProduct()->getName() : "null")." - ".($this->getCategory() ? $this->getCategory()->getName() : "null");
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setMain($main)
    {
        $this->main = $main;
    }

    /**
     * {@inheritdoc}
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }
}
