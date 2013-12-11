<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\CoreBundle\Entity\DoctrineBaseManager;

class AddressManager extends DoctrineBaseManager implements AddressManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function setCurrent(AddressInterface $address)
    {
        foreach ($address->getCustomer()->getAddressesByType($address->getType()) as $custAddress) {
            if ($custAddress->getCurrent()) {
                $custAddress->setCurrent(false);
                $this->save($custAddress);
                break;
            }
        }

        $address->setCurrent(true);
        $this->save($address);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($address, $andFlush = true)
    {
        if ($address->getCurrent()) {
            $custAddresses = $address->getCustomer()->getAddressesByType(AddressInterface::TYPE_DELIVERY);

            if (count($custAddresses) > 1) {
                foreach ($custAddresses as $currentAddress) {
                    if ($currentAddress->getId() !== $address->getId()) {
                        $currentAddress->setCurrent(true);
                        $this->save($currentAddress);
                        break;
                    }
                }
            }
        }

        parent::delete($address, $andFlush);
    }
}
