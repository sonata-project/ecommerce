<?php
namespace Sonata\Component\Currency;

use Sonata\Component\Currency\CurrencyPriceCalculatorInterface;
use Sonata\Component\Product\ProductInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculator implements CurrencyPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductInterface $product, CurrencyInterface $currency)
    {
        return $product->getPrice();
    }
}