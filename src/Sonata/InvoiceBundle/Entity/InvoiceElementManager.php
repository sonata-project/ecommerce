<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\InvoiceBundle\Entity;

use Sonata\Component\Invoice\InvoiceElementManagerInterface;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class InvoiceElementManager implements InvoiceElementManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if (class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty invoiceElement instance
     *
     * @return InvoiceElement
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a invoiceElement
     *
     * @param  InvoiceElementInterface $invoice
     * @return void
     */
    public function save(InvoiceElementInterface $invoice)
    {
        $this->em->persist($invoice);
        $this->em->flush();
    }

    /**
     * Returns the invoiceElement's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one InvoiceElement by the given criteria
     *
     * @param  array          $criteria
     * @return InvoiceElement
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds many InvoiceElements by the given criteria
     *
     * @param  array          $criteria
     * @return InvoiceElement
     */
    public function findBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Deletes a invoice
     *
     * @param  InvoiceElementInterface $invoice
     * @return void
     */
    public function delete(InvoiceElementInterface $invoice)
    {
        $this->em->remove($invoice);
        $this->em->flush();
    }
}
