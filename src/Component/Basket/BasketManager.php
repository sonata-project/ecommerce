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
            return $this->getRepository()->createQueryBuilder('b')
                ->leftJoin('b.basketElements', 'be', null, null, 'be.position')
                ->where('b.customer = :customer')
                ->setParameter('customer', $customer->getId())
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, $andFlush = true)
    {
        foreach ($entity->getBasketElements() as $element) {
            $element->setBasket($entity);
        }

        parent::save($entity, $andFlush);
    }
}
