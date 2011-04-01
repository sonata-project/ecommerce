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
    protected $em;
    protected $repository;
    protected $class;
    
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if(class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty invoiceElement instance
     *
     * @return InvoiceElement
     */
    public function createInvoiceElement()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a invoiceElement
     *
     * @param InvoiceElement $invoice
     * @return void
     */
    public function updateInvoiceElement(InvoiceElementInterface $invoice)
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
     * Finds one invoiceElement by the given criteria
     *
     * @param array $criteria
     * @return InvoiceElement
     */
    public function findInvoiceElementBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a invoice
     *
     * @param InvoiceElement $invoiceElement
     * @return void
     */
    public function deleteInvoiceElement(InvoiceElementInterface $invoice)
    {
        $this->em->remove($invoice);
        $this->em->flush();
    }
}