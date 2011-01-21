<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Application\ProductBundle\Entity\{{ product }};

use Application\ProductBundle\Product\{{ product }}\{{ product }}AddBasketForm;
use Application\ProductBundle\Product\{{ product }}\{{ product }}AddBasket;

/**
 *
 * overwrite methods from the BaseProductController if you want to change the behavior
 * for the current product
 * 
 */
class {{ product }}Controller extends \Sonata\ProductBundle\Controller\BaseProductController
{

}