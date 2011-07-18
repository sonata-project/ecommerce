<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Application\Sonata\ProductBundle\Entity\{{ product }};

use Application\Sonata\ProductBundle\Product\{{ product }}\{{ product }}AddBasket;

/**
 *
 * overwrite methods from the BaseProductController if you want to change the behavior
 * for the current product
 *
 */
class {{ product }}Controller extends \Sonata\ProductBundle\Controller\BaseProductController
{

}