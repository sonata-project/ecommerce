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
class UnavailableForCurrencyException extends \Exception
{
    public function __construct(ProductInterface $product, CurrencyInterface $currency)
    {
        parent::__construct(sprintf("Product '%s' is not available for currency '%s'", $product->getName(), $currency->getLabel()));
    }
}
