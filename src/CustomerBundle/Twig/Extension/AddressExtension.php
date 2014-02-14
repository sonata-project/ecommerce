<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\CustomerBundle\Twig\Extension;

use Sonata\Component\Customer\AddressInterface;
use Sonata\CoreBundle\Exception\InvalidParameterException;
use Sonata\CustomerBundle\Entity\BaseAddress;


/**
 * Class AddressExtension
 *
 * @package Sonata\CustomerBundle\Twig
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'sonata_address_render',
                array($this, 'renderAddress'),
                array(
                    'needs_environment' => true,
                    'is_safe'           => array('html')
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_address';
    }

    /**
     * Gets the HTML of an address
     *
     * @param \Twig_Environment $environment
     * @param mixed             $address Instance of AddressInterface or array with keys: (id, firstname, lastname, address1, postcode, city, country_code and optionally name, address2, address3)
     * @param bool              $showName
     * @param bool              $showEdit
     */
    public function renderAddress(\Twig_Environment $environment, $address, $showName = true, $showEdit = false)
    {
        $requiredAddressKeys = array("firstname", "lastname", "address1", "postcode", "city", "country_code");

        if (!($address instanceof AddressInterface) && (!is_array($address) ||  count(array_diff($requiredAddressKeys, array_keys($address))) !== 0)) {
            throw new InvalidParameterException(sprintf("sonata_address_render needs an AddressInterface instance or an array with keys (%s)", implode(", ", $requiredAddressKeys)));
        }

        if ($address instanceof AddressInterface) {
            $addressArray = array(
                'id'        => $showEdit ? $address->getId() : "",
                'name'      => $showName ? $address->getName() : "",
                'address'   => $address->getFullAddressHtml()
            );
        } else {
            if ($showEdit && !array_key_exists("id", $address)) {
                throw new InvalidParameterException("sonata_address_render needs 'id' key to be set to render the edit button");
            }

            if ($showName && !array_key_exists('name', $address)) {
                $address["name"] = "";
                $showName = false;
            }

            $addressArray = array(
                'id'      => $showEdit ? $address['id'] : "",
                'name'    => $address['name'],
                'address' => BaseAddress::formatAddress($address, "<br/>")
            );
        }

        return $environment->render('SonataCustomerBundle:Addresses:_address.html.twig', array(
                'address'  => $addressArray,
                'showName' => $showName,
                'showEdit' => $showEdit
            )
        );
    }
}