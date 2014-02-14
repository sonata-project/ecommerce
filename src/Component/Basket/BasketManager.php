<?php

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;
use Doctrine\ORM\NoResultException;
use Sonata\CoreBundle\Model\BaseEntityManager;

class BasketManager extends BaseEntityManager implements BasketManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadBasketPerCustomer(CustomerInterface $customer)
    {
        try {
            return $this->getRepository()->createQueryBuilder()
                ->select('b, be')
                ->from($this->class, 'b')
                ->leftJoin('b.basketElements', 'be', null, null, 'be.position')
                ->where('b.customer = :customer')
                ->setParameter('customer', $customer->getId())
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
