<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GlobalVariables
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getBasket()
    {
        return $this->container->get('sonata.basket');
    }

    public function getCustomer()
    {
        return $this->getBasket()->getCustomer();
    }
}
