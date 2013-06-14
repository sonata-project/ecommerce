<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

interface PackageManagerInterface
{
    /**
     * Creates an empty medie instance
     *
     * @return Package
     */
    public function createPackage();

    /**
     * Deletes a package
     *
     * @param  Package $package
     * @return void
     */
    public function deletePackage(PackageInterface $package);

    /**
     * Finds one package by the given criteria
     *
     * @param  array            $criteria
     * @return PackageInterface
     */
    public function findPackageBy(array $criteria);

    /**
     * Returns the package's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a package
     *
     * @param  Package $package
     * @return void
     */
    public function updatePackage(PackageInterface $package);
}
