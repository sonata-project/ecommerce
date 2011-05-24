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

class ProductDefinition
{
    protected $manager;

    protected $provider;

    public function __construct(ProductProviderInterface $provider, ProductManagerInterface $manager)
    {
        $this->provider = $provider;
        $this->manager  = $manager;
    }

    /**
     * @return \Sonata\Component\ProductProductManagerInterface
     */
    public function getManager()
    {
      return $this->manager;
    }

    /**
     * @return \Sonata\Component\ProductProviderInterface
     */
    public function getProvider()
    {
      return $this->provider;
    }

}