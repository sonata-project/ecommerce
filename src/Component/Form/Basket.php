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

namespace Sonata\Component\Form;

use Symfony\Component\Validator\Constraint;

class Basket extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Basket not valid';

    public function validatedBy()
    {
        return 'sonata_basket_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
