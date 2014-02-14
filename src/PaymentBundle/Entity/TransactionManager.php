<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\PaymentBundle\Entity;

use Sonata\Component\Payment\TransactionManagerInterface;
use Sonata\Component\Payment\TransactionInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sonata\CoreBundle\Model\BaseEntityManager;

class TransactionManager extends BaseEntityManager implements TransactionManagerInterface
{
}
