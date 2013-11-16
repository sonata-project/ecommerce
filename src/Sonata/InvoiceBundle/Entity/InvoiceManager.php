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

use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Doctrine\ORM\EntityManager;

class InvoiceManager implements InvoiceManagerInterface
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
     * Creates an empty Invoice instance
     *
     * @return Invoice
     */
    public function createInvoice()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a invoice
     *
     * @param  InvoiceInterface $invoice
     * @return void
     */
    public function updateInvoice(InvoiceInterface $invoice)
    {
        $this->em->persist($invoice);
        $this->em->flush();
    }

    /**
     * Returns the invoice's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one invoice by the given criteria
     *
     * @param  array   $criteria
     * @return Invoice
     */
    public function findInvoiceBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a invoice
     *
     * @param  InvoiceInterface $invoice
     * @return void
     */
    public function deleteInvoice(InvoiceInterface $invoice)
    {
        $this->em->remove($invoice);
        $this->em->flush();
    }
}
