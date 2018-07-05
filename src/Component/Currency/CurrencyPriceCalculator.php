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

namespace Sonata\Component\Currency;

use Sonata\Component\Product\ProductInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculator implements CurrencyPriceCalculatorInterface
{
    public function getPrice(ProductInterface $product, CurrencyInterface $currency, $vat = false)
    {
        $price = $product->getPrice();

        if (!$vat && true === $product->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv($product->getVatRate(), '100')));
        }

        if ($vat && false === $product->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv($product->getVatRate(), '100')));
        }

        return $price;
    }
}
