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

class Pool
{
    protected $products = array();

    /**
     * add a delivery method into the pool
     *
     * @param string $code
     * @param \Sonata\Component\Product\ProductDefinition $productDescription
     * @return void
     */
    public function addProduct($code, ProductDefinition $productDescription)
    {
        $this->products[$code] = $productDescription;
    }

    /**
     * @param ProductInterface|string $code
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
     * @return int|null|string
     */
    public function getProductCode(ProductInterface $product)
    {
        $class = get_class($product);

        foreach ($this->products as $code => $productDescription) {
            if ($productDescription->getManager()->getClass() == $class) {
                return $code;
            }
        }

        return null;
    }

    /**
     * @param string $code
     * @return boolean
     */
    public function hasProvider($code)
    {
        return isset($this->products[$code]);
    }

    /**
     *
     * @param string $code
     * @return boolean
     */
    public function hasProduct($code)
    {
        return isset($this->products[$code]);
    }

    /**
     *
     * @param string $code
     * @return \Sonata\Component\Product\ProductDescription
     */
    public function getProduct($code)
    {
        if (!$this->hasProduct($code)) {
            throw new \RuntimeException(sprintf('The product `%s` does not exist!', $code));
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