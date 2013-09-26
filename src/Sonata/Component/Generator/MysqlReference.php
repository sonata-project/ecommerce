<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Generator;

use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderInterface;
use Doctrine\ORM\EntityManager;

class MysqlReference implements ReferenceInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function invoice(InvoiceInterface $invoice)
    {
        if (!$invoice->getId()) {
            throw new \RuntimeException('The invoice is not persisted into the database');
        }

        $tableName = $this->entityManager->getClassMetadata(get_class($invoice))->table['name'];
        $connection = $this->entityManager->getConnection();

        $this->generateReference($tableName, $connection, $invoice);
    }

    /**
     * {@inheritdoc}
     */
    public function order(OrderInterface $order)
    {
        if (!$order->getId()) {
            throw new \RuntimeException('The order is not persisted into the database');
        }

        $tableName = $this->entityManager->getClassMetadata(get_class($order))->table['name'];
        $connection = $this->entityManager->getConnection();

        $this->generateReference($tableName, $connection, $order);
    }

    /**
     * generate a correct reference number
     *
     * @param $tableName
     * @param $connection
     * @param $object
     *
     * @throws \Exception
     *
     * @return Exception|string
     */
    protected function generateReference($tableName, $connection, $object)
    {
        $date = new \DateTime;

        $sql = sprintf("SELECT count(id) as counter FROM %s WHERE created_at >= '%s' AND reference IS NOT NULL", $tableName, $date->format('Y-m-d'));

        $connection->exec(sprintf('LOCK TABLES %s WRITE', $tableName));

        try {
            $statement = $connection->query($sql);
            $row = $statement->fetch();

            $reference = sprintf('%02d%02d%02d%06d',
                $date->format('y'),
                $date->format('n'),
                $date->format('j'),
                $row['counter'] + 1
            );

            $connection->update($tableName, array('reference' => $reference), array('id' => $object->getId()));
            $object->setReference($reference);

        } catch (\Exception $e) {
            $connection->exec(sprintf('UNLOCK TABLES'));

            throw $e;
        }

        $connection->exec(sprintf('UNLOCK TABLES'));

        return $reference;
    }
}
