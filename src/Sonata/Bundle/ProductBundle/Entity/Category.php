<?php

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\Category
 */
class Category
{
    /**
     * @var integer $rank
     */
    private $rank;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var text $main_description
     */
    private $main_description;

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
     * @var integer $id
     */
    private $id;

    /**
     * Set rank
     *
     * @param integer $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get rank
     *
     * @return integer $rank
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set main_description
     *
     * @param text $mainDescription
     */
    public function setMainDescription($mainDescription)
    {
        $this->main_description = $mainDescription;
    }

    /**
     * Get main_description
     *
     * @return text $mainDescription
     */
    public function getMainDescription()
    {
        return $this->main_description;
    }

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
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}