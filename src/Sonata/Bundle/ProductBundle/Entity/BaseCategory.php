<?php

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\BaseCategory
 */
abstract class BaseCategory
{

    /**
     * @var string $label
     */
    private $name;

    /**
     * @var text $sub_description
     */
    private $sub_description;

    /**
     * @var boolean $enabled
     */
    private $enabled;

    /**
     * @var datetime $updated_at
     */
    private $updated_at;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $parent_id
     */
    private $parent_id;

    /**
     * @var Application\ProductBundle\Entity\Category
     */
    private $children;

    /**
     * @var Application\ProductBundle\Entity\Category
     */
    private $parent;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var integer $position
     */
    private $position;

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
     * Set parent_id
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    }

    /**
     * Get parent_id
     *
     * @return integer $parentId
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Add children
     *
     * @param Application\ProductBundle\Entity\Category $children
     */
    public function addChildren(\Application\ProductBundle\Entity\Category $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param Application\ProductBundle\Entity\Category $parent
     */
    public function setParent(\Application\ProductBundle\Entity\Category $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Application\ProductBundle\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->parent;
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
}