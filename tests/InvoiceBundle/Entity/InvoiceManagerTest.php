<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\InvoiceBundle\Entity\BaseInvoice;
use Sonata\InvoiceBundle\Entity\InvoiceManager;

/**
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class InvoiceManagerTest extends TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['i']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('i.reference'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\InvoiceBundle\Entity\BaseInvoice' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['i']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('i.reference'),
                        $self->equalTo('i.name')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1, 10, [
                'reference' => 'ASC',
                'name' => 'DESC',
            ]);
    }

    public function testGetPagerWithOpenInvoices()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['i']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('i.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseInvoice::STATUS_OPEN]));
            })
            ->getPager(['status' => BaseInvoice::STATUS_OPEN], 1);
    }

    public function testGetPagerWithPaidInvoices()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['i']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('i.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseInvoice::STATUS_PAID]));
            })
            ->getPager(['status' => BaseInvoice::STATUS_PAID], 1);
    }

    public function testGetPagerWithConflictInvoices()
    {
        $self = $this;
        $this
            ->getInvoiceManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['i']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('i.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseInvoice::STATUS_CONFLICT]));
            })
            ->getPager(['status' => BaseInvoice::STATUS_CONFLICT], 1);
    }

    protected function getInvoiceManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'reference',
            'status',
            'name',
        ]);

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new InvoiceManager('Sonata\InvoiceBundle\Entity\BaseInvoice', $registry);
    }
}
