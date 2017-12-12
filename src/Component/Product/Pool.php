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

class Pool
{
    /**
     * @var array
     */
    protected $products = [];

    /**
     * add a delivery method into the pool.
     *
     * @param string                                      $code
     * @param \Sonata\Component\Product\ProductDefinition $productDescription
     */
    public function addProduct($code, ProductDefinition $productDescription): void
    {
        $this->products[$code] = $productDescription;
    }

    /**
     * @param ProductInterface|string $code
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Product\ProductProviderInterface
     */
    public function getProvider($code)
    {
        if ($code instanceof ProductInterface) {
            $code = $this->getProductCode($code);

            if (!$code) {
                throw new \RuntimeException(sprintf('The class is not linked to a ProductProvider!'));
            }
        }

        return $this->getProduct($code)->getProvider();
    }

    /**
     * @param ProductInterface|string $code
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Product\ProductManagerInterface
     */
    public function getManager($code)
    {
        if ($code instanceof ProductInterface) {
            $code = $this->getProductCode($code);

            if (!$code) {
                throw new \RuntimeException(sprintf('The class is not linked to a ProductManager!'));
            }
        }

        return $this->getProduct($code)->getManager();
    }

    /**
     * @param ProductInterface $product
     *
     * @return int|null|string
     */
    public function getProductCode(ProductInterface $product)
    {
        foreach ($this->products as $code => $productDescription) {
            $className = $productDescription->getManager()->getClass();
            if ($product instanceof $className) {
                return $code;
            }
        }
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function hasProvider($code)
    {
        return isset($this->products[$code]);
    }

    /**
     * Tells if a product with $code is in the pool.
     *
     * @param string $code
     *
     * @return bool
     */
    public function hasProduct($code)
    {
        return isset($this->products[$code]);
    }

    /**
     * @param string $code
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Product\ProductDefinition
     */
    public function getProduct($code)
    {
        if (!$this->hasProduct($code)) {
            throw new \RuntimeException(sprintf('The product definition `%s` does not exist!', $code));
        }

        return $this->products[$code];
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
}
