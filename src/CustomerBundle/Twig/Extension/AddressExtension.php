<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Twig\Extension;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\ServiceDeliverySelectorInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressExtension extends \Twig_Extension
{
    /**
     * @var ServiceDeliverySelectorInterface
     */
    protected $deliverySelector;

    public function __construct(ServiceDeliverySelectorInterface $deliverySelector)
    {
        $this->deliverySelector = $deliverySelector;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'sonata_address_render',
                [$this, 'renderAddress'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction('sonata_address_deliverable', [$this, 'isAddressDeliverable']),
        ];
    }

    public function getName()
    {
        return 'sonata_address';
    }

    /**
     * Gets the HTML of an address.
     *
     * @param \Twig_Environment $environment A Twig environment
     * @param mixed             $address     An instance of AddressInterface or array with keys: (id, firstname, lastname, address1, postcode, city, country_code and optionally name, address2, address3)
     * @param bool              $showName    Display address name?
     * @param bool              $showEdit    Display edit button?
     * @param string            $context     A context for edit link
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function renderAddress(\Twig_Environment $environment, $address, $showName = true, $showEdit = false, $context = null)
    {
        $requiredAddressKeys = ['firstname', 'lastname', 'address1', 'postcode', 'city', 'country_code'];

        if (!($address instanceof AddressInterface) && (!\is_array($address) || 0 !== \count(array_diff($requiredAddressKeys, array_keys($address))))) {
            throw new \RuntimeException(sprintf('sonata_address_render needs an AddressInterface instance or an array with keys (%s)', implode(', ', $requiredAddressKeys)));
        }

        if ($address instanceof AddressInterface) {
            $addressArray = [
                'id' => $showEdit ? $address->getId() : '',
                'name' => $showName ? $address->getName() : '',
                'address' => $address->getFullAddressHtml(),
            ];
        } else {
            if ($showEdit && !\array_key_exists('id', $address)) {
                throw new \RuntimeException("sonata_address_render needs 'id' key to be set to render the edit button");
            }

            if ($showName && !\array_key_exists('name', $address)) {
                $address['name'] = '';
                $showName = false;
            }

            $addressArray = [
                'id' => $showEdit ? $address['id'] : '',
                'name' => $address['name'],
                'address' => BaseAddress::formatAddress($address, '<br/>'),
            ];
        }

        return $environment->render('@SonataCustomer/Addresses/_address.html.twig', [
                'address' => $addressArray,
                'showName' => $showName,
                'showEdit' => $showEdit,
                'context' => $context,
            ]
        );
    }

    /**
     * Returns if address can deliver the given basket.
     *
     * @param AddressInterface $address A Sonata e-commerce address instance
     * @param BasketInterface  $basket  A Sonata e-commerce basket instance
     *
     * @return bool
     */
    public function isAddressDeliverable(AddressInterface $address, BasketInterface $basket)
    {
        $methods = $this->deliverySelector->getAvailableMethods($basket, $address);

        return \count($methods) > 0 ? true : false;
    }
}
