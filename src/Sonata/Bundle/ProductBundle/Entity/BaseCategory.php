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

/**
 * Sonata\Bundle\ProductBundle\Entity\BaseCategory
 */
abstract class BaseCategory
{

    /**
     * @var string $label
     */
    protected $name;

    /**
     * @var text $sub_description
     */
    protected $sub_description;

    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var datetime $updated_at
     */
    protected $updated_at;

    /**
     * @var datetime $created_at
     */
    protected $created_at;

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

    protected $Children;

    protected $Parent;

    /**
     * Set sub_description
     *
     * @param text $subDescription
     */
    public function setSubDescription($subDescription)
    {
        $this->sub_description = $subDescription;
    }

    /**
     * Get sub_description
     *
     * @return text $subDescription
     */
    public function getSubDescription()
    {
        return $this->sub_description;
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
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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
     * @param Application\ProductBundle\Entity\Category $children
     */
    public function addChildren(\Application\ProductBundle\Entity\Category $children, $nested = false)
    {
        $this->Children[] = $children;

        if(!$nested) {
            $children->setParent($this, true);
        }
    }

    /**
     * Get Children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->Children;
    }

    /**
     * Set Parent
     *
     * @param Application\ProductBundle\Entity\Category $parent
     */
    public function setParent(\Application\ProductBundle\Entity\Category $parent, $nested = false)
    {
        $this->Parent = $parent;

        if(!$nested) {
            $parent->addChildren($this, true);
        }
    }

    /**
     * Get Parent
     *
     * @return Application\ProductBundle\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->Parent;
    }
}