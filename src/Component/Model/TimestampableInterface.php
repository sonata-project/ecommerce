<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\Component\Model;

/**
 * Interface TimestampableInterface
 *
 * @package Sonata\Component\Model
 *
 * @author  Alexandru Furculita <alex@rhetina.com>
 */
interface TimestampableInterface
{
    /**
     * Get creation time.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get the time of last update.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set creation time.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Set the time of last update.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);
}