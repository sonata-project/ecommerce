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

namespace Sonata\BasketBundle\Twig;

use Sonata\Component\Basket\Basket;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GlobalVariables
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this->container->get('sonata.basket');
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->getBasket()->getCustomer();
    }
}
