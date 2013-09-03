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
     * {@inheritDoc}
     */
    public function getPriceForProductInCurrency(ProductInterface $product, CurrencyInterface $currency)
    {
        // TODO Auto-generated method stub
    }
}