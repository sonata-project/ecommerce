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

class MysqlReference implements ReferenceInterface
{

    protected $entityManager;

    /**
     * Append a valid reference number to the invoice, the order must be persisted first
     *
     * @param Invoice $invoice
     */
    public function invoice($invoice)
    {

        if (!$invoice->getId()) {
            throw new \RuntimeException('The invoice is not persisted into the database');
        }

        $table_name = $this->getEntityManager()->getClassMetadata(get_class($invoice))->table['name'];
        $connection = $this->getEntityManager()->getConnection();

        $this->generateReference($table_name, $connection, $invoice);

    }

    /**
     * Append a valid reference number to the order, the order must be persisted first
     *
     * @param Order $order
     */
    public function order($order)
    {
        if (!$order->getId()) {
            throw new \RuntimeException('The order is not persisted into the database');
        }

        $table_name = $this->getEntityManager()->getClassMetadata(get_class($order))->table['name'];
        $connection = $this->getEntityManager()->getConnection();

        $this->generateReference($table_name, $connection, $order);
       
    }

    /**
     * generate a correct reference number
     *
     * @param  $table_name
     * @param  $connection
     * @param  $object
     * @return Exception|string
     */
    protected function generateReference($table_name, $connection, $object)
    {
        $date = new \DateTime;

        $sql = sprintf("SELECT count(id) as counter FROM %s WHERE created_at >= '%s' AND reference IS NOT NULL", $table_name, $date->format('Y-m-d'));

        $connection->exec(sprintf('LOCK TABLES %s WRITE', $table_name));

        try {
            $statement = $connection->query($sql);
            $row = $statement->fetch();

            $reference = sprintf('%02d%02d%02d%04d',
              $date->format('y'),
              $date->format('n'),
              $date->format('j'),
              $row['counter'] + 1
            );

            $connection->update($table_name, array('reference' => $reference), array('id' => $object->getId()));
            $object->setReference($reference);

        } catch(\Exception $e) {
            $connection->exec(sprintf('UNLOCK TABLES'));

            return $e;
        }

        $connection->exec(sprintf('UNLOCK TABLES'));

        return $reference;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

}