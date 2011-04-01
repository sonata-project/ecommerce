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

use Sonata\Component\Product\CategoryInterface;

abstract class BaseCategory implements CategoryInterface
{

    /**
     * @var string $label
     */
    protected $name;

    /**
     * @var text $sub_description
     */
    protected $subDescription;

    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * @var string $slug
     */
    protected $slug;

    /**
     * @var integer $position
     */
    protected $position;

    protected $children;

    protected $parent;

    /**
     * Set sub_description
     *
     * @param text $subDescription
     */
    public function setSubDescription($subDescription)
    {
        $this->subDescription = $subDescription;
    }

    /**
     * Get sub_description
     *
     * @return text $subDescription
     */
    public function getSubDescription()
    {
        return $this->subDescription;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return integer $position
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Add Children
     *
     * @param Application\Sonata\ProductBundle\Entity\Category $children
     */
    public function addChildren(CategoryInterface $children, $nested = false)
    {
        $this->children[] = $children;

//        if (!$nested) {
//            $children->setParent($this, true);
//        }
    }

    public function disableChildrenLazyLoading()
    {
        if (is_object($this->children))
        {
            $this->children->setInitialized(true);
        }
    }

    /**
     * Get Children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     *
     * @return boolean
     */
    public function hasChildren()
    {
        
        return count($this->children) > 0;
    }

    /**
     * Set Parent
     *
     * @param CategoryInterface
     * @param boolean
     */
    public function setParent(CategoryInterface $parent, $nested = false)
    {
        $this->parent = $parent;

        if (!$nested) {
            $parent->addChildren($this, true);
        }
    }

    /**
     * Get Parent
     *
     * @return Application\Sonata\ProductBundle\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->parent;
    }
}