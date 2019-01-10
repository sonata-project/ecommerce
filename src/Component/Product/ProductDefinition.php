<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

class ProductDefinition
{
    /**
     * @var ProductManagerInterface
     */
    protected $manager;

    /**
     * @var ProductProviderInterface
     */
    protected $provider;

    /**
     * @param ProductProviderInterface $provider
     * @param ProductManagerInterface  $manager
     */
    public function __construct(ProductProviderInterface $provider, ProductManagerInterface $manager)
    {
        $this->provider = $provider;
        $this->manager = $manager;
    }

    /**
     * @return ProductManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return ProductProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
