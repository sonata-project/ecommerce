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
     * @param text $subDescription
     */
    function setSubDescription($subDescription);

    /**
     * Get sub_description
     *
     * @return text $subDescription
     */
    function getSubDescription();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    function getEnabled();

    /**
     * Set updatedAt
     *
     * @param Datetime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return Datetime $updatedAt
     */
    function getUpdatedAt();

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return Datetime $createdAt
     */
    function getCreatedAt();

    /**
     * Set name
     *
     * @param string $name
     */
    function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    function getName();

    /**
     * Set description
     *
     * @param text $description
     */
    function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    function getDescription();

    /**
     * Set RAW description.
     *
     * @param text $rawDescription
     */
    function setRawDescription($rawDescription);

    /**
     * Get RAW description.
     *
     * @return text $rawDescription
     */
    function getRawDescription();

    /**
     * Set description formatter.
     *
     * @param text $descriptionFormatter
     */
    function setDescriptionFormatter($descriptionFormatter);

    /**
     * Get description formatter.
     *
     * @return text $descriptionFormatter
     */
    function getDescriptionFormatter();

    /**
     * Set slug
     *
     * @param string $slug
     */
    function setSlug($slug);

    /**
     * Get slug
     *
     * @return string $slug
     */
    function getSlug();

    /**
     * Set position
     *
     * @param integer $position
     */
    function setPosition($position);

    /**
     * Get position
     *
     * @return integer $position
     */
    function getPosition();

    /**
     * Add Children
     *
     * @param CaregoryInterface $children
     * @param boolean $nested
     */
    function addChildren(CategoryInterface $children, $nested = false);

    /**
     * Get Children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    function getChildren();

    /**
     *
     * @return void
     */
    function setChildren($children);

    /**
     *
     * @return boolean
     */
    function hasChildren();

    /**
     * Set Parent
     *
     * @param CaregoryInterface $parent
     */
    function setParent(CategoryInterface $parent, $nested = false);

    /**
     * Get Parent
     *
     * @return CaregoryInterface
     */
    function getParent();
}