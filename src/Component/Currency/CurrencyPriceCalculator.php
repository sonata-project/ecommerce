<?php

namespace Sonata\Component\Currency;

use Sonata\Component\Product\ProductInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculator implements CurrencyPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductInterface $product, CurrencyInterface $currency, $vat = false)
    {
        $price = $product->getPrice();

        if ($vat && false === $product->isPriceIncludingVat()) {
            $price = $price * (1 + $product->getVatRate() / 100);
        }

        if (!$vat && true === $product->isPriceIncludingVat()) {
            $price = $price * (1 - $product->getVatRate() / 100);
        }

        return $price;
    }
}
