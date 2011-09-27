<?php

namespace Sonata\Component\Basket;

interface BasketElementManagerInterface
{
    /**
     * Creates an empty basket element instance
     *
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    function create();

    /**
     * Updates a basket
     *
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return void
     */
    function save(BasketElementInterface $basketElement);

    /**
     * Finds one basket element by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    function findOneBy(array $criteria);

    /**
     * Returns the basket element's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Finds many basket elements by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketElementInterface[]
     */
    function findBy(array $criteria);

    /**
     * Deletes a basket element
     *
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return void
     */
    function delete(BasketElementInterface $basketElement);
}
