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

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Product\CategoryInterface;

abstract class BaseCategory implements CategoryInterface
{
    /**
     * @var string $label
     */
    protected $name;

    /**
     * @var string $sub_description
     */
    protected $subDescription;

    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var string $rawDescription
     */
    protected $rawDescription;

    /**
     * @var string $descriptionFormatter
     */
    protected $descriptionFormatter;

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

    protected $productCategories;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set sub_description
     *
     * @param string $subDescription
     */
    public function setSubDescription($subDescription)
    {
        $this->subDescription = $subDescription;
    }

    /**
     * Get sub_description
     *
     * @return string $subDescription
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
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
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

        if (!$this->getSlug()) {
            $this->setSlug($name);
        }
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
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set RAW description.
     *
     * @param string $rawDescription
     */
    public function setRawDescription($rawDescription)
    {
        $this->rawDescription = $rawDescription;
    }

    /**
     * Get RAW description.
     *
     * @return string $rawDescription
     */
    public function getRawDescription()
    {
        return $this->rawDescription;
    }

    /**
     * Set description formatter.
     *
     * @param string $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter)
    {
        $this->descriptionFormatter = $descriptionFormatter;
    }

    /**
     * Get description formatter.
     *
     * @return string $descriptionFormatter
     */
    public function getDescriptionFormatter()
    {
        return $this->descriptionFormatter;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = self::slugify(trim($slug));
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
     * source : http://snipplr.com/view/22741/slugify-a-string-in-php/
     *
     * @static
     * @param  $text
     * @return mixed|string
     */
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
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
     * @param CategoryInterface $children
     * @param boolean           $nested
     */
    public function addChildren(CategoryInterface $children, $nested = false)
    {
        $this->children[] = $children;

        if (!$nested) {
            $children->setParent($this, true);
        }
    }

    public function disableChildrenLazyLoading()
    {
        if (is_object($this->children)) {
            $this->children->setInitialized(true);
        }
    }

    /**
     * Get Children
     *
     * @return \Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param $children
     */
    public function setChildren($children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChildren($category);
        }
    }

    /**
     * Return true if category has children
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
     * @param CategoryInterface $parent
     * @param boolean           $nested
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
     * @return \Application\Sonata\ProductBundle\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }
}
