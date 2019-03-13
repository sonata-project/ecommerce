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

use Sonata\Doctrine\Entity\BaseEntityManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyManager extends BaseEntityManager implements CurrencyManagerInterface
{
    public function findOneByLabel($currencyLabel)
    {
        $currency = new Currency();
        $currency->setLabel($currencyLabel);

        return $currency;
    }
}
