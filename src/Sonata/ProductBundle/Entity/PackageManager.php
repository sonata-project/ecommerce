<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\PackageManagerInterface;
use Sonata\Component\Product\PackageInterface;
use Doctrine\ORM\EntityManager;

class PackageManager implements PackageManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;

    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if (class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty package instance
     *
     * @return package
     */
    public function createPackage()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a package
     *
     * @param  Package $package
     * @return void
     */
    public function updatePackage(PackageInterface $package)
    {
        $this->em->persist($package);
        $this->em->flush();
    }

    /**
     * Returns the package's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one package by the given criteria
     *
     * @param  array   $criteria
     * @return Package
     */
    public function findPackageBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a package
     *
     * @param  Package $package
     * @return void
     */
    public function deletePackage(PackageInterface $package)
    {
        $this->em->remove($package);
        $this->em->flush();
    }
}
