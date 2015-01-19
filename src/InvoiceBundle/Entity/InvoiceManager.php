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
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class InvoiceManager extends BaseEntityManager implements InvoiceManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $query = $this->getRepository()
            ->createQueryBuilder('i')
            ->select('i');

        $fields = $this->getEntityManager()->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }
        if (count($sort) == 0) {
            $sort = array('reference' => 'ASC');
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('i.%s', $field), strtoupper($direction));
        }

        $parameters = array();

        if (isset($criteria['status'])) {
            $query->andWhere('i.status = :status');
            $parameters['status'] = $criteria['status'];
        }

        $query->setParameters($parameters);
        $pager = new Pager();

        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
