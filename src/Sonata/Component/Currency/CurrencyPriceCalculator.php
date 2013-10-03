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
        if (null === $product->getCurrency() || !$currency->equals($product->getCurrency())) {
            throw new UnavailableForCurrencyException($product, $currency);
        }

        return $product->getPrice();
    }
}