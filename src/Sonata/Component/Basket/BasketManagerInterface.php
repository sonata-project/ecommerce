<?php

namespace Sonata\Component\Basket;

interface BasketManagerInterface
{
    /**
     * Creates an empty basket instance
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    function create();

    /**
     * Updates a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    function save(BasketInterface $basket);

    /**
     * Finds one basket by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketInterface
     */
    function findOneBy(array $criteria);

    /**
     * Returns the basket's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Finds many baskets by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketInterface[]
     */
    function findBy(array $criteria);

    /**
     * Deletes a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    function delete(BasketInterface $basket);
}
