<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

interface CategoryInterface
{
    /**
     * Set sub_description
     *
     * @param string $subDescription
     */
    public function setSubDescription($subDescription);

    /**
     * Get sub_description
     *
     * @return string $subDescription
     */
    public function getSubDescription();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled();

    /**
     * Set updatedAt
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Set createdAt
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription();

    /**
     * Set RAW description.
     *
     * @param string $rawDescription
     */
    public function setRawDescription($rawDescription);

    /**
     * Get RAW description.
     *
     * @return string $rawDescription
     */
    public function getRawDescription();

    /**
     * Set description formatter.
     *
     * @param string $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter);

    /**
     * Get description formatter.
     *
     * @return string $descriptionFormatter
     */
    public function getDescriptionFormatter();

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug();

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position);

    /**
     * Get position
     *
     * @return integer $position
     */
    public function getPosition();

    /**
     * Add Children
     *
     * @param CategoryInterface $children
     * @param boolean           $nested
     */
    public function addChildren(CategoryInterface $children, $nested = false);

    /**
     * Get Children
     *
     * @return array $children
     */
    public function getChildren();

    /**
     *
     * @return void
     */
    public function setChildren($children);

    /**
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * Set Parent
     *
     * @param CategoryInterface $parent
     * @param boolean           $nested
     */
    public function setParent(CategoryInterface $parent, $nested = false);

    /**
     * Get Parent
     *
     * @return CategoryInterface
     */
    public function getParent();
}
