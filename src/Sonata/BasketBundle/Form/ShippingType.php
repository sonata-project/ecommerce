<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Form;

use Sonata\Component\Delivery\ServiceDeliverySelectorInterface;
use Symfony\Component\Form\AbstractType;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Form\Transformer\DeliveryMethodTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingType extends AbstractType
{
    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var DeliveryPool
     */
    protected $deliveryPool;

    /**
     * @var ServiceDeliverySelectorInterface
     */
    protected $deliverySelector;

    /**
     * @var ModelManagerInterface
     */
    protected $modelManager;

    /**
     * Constructor
     *
     * @param AddressManagerInterface          $addressManager
     * @param ModelManagerInterface            $modelManager
     * @param DeliveryPool                     $deliveryPool
     * @param ServiceDeliverySelectorInterface $deliverySelector
     */
    public function __construct(AddressManagerInterface $addressManager, ModelManagerInterface $modelManager, DeliveryPool $deliveryPool, ServiceDeliverySelectorInterface $deliverySelector)
    {
        $this->addressManager   = $addressManager;
        $this->modelManager     = $modelManager;
        $this->deliverySelector = $deliverySelector;
        $this->deliveryPool     = $deliveryPool;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        $addresses = $this->addressManager->findBy(array(
            'customer'      => $basket->getCustomer()->getId(),
            'type'          => AddressInterface::TYPE_DELIVERY
        ));

//         $builder->add('deliveryAddress', 'sonata_type_model', array(
//             'model_manager' => $this->modelManager,
//             'class'         => $this->addressManager->getClass(),
//             'choices'       => $addresses,
//             'expanded'      => true
//         ));

//         if (count($addresses) > 0) {
//             $builder->add('deliveryAddress', 'choice', array(
//                 'choices'       => $addresses,
//                 'expanded'      => true
//             ));
//         } else {
//             $builder->add('deliveryAddress', 'sonata_basket_address');
//         }

        $address = $basket->getDeliveryAddress() ?: current($addresses);
        $basket->setDeliveryAddress($address ?: null);

        $methods = $this->deliverySelector->getAvailableMethods($basket, $basket->getDeliveryAddress());

        $choices = array();
        foreach ($methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        reset($methods);

        $method = $basket->getDeliveryMethod() ?: current($methods);
        $basket->setDeliveryMethod($method ?: null);

        $sub = $builder->create('deliveryMethod', 'choice', array(
            'expanded'  => true,
            'choice_list'   => new SimpleChoiceList($choices),
        ));

        $sub->addViewTransformer(new DeliveryMethodTransformer($this->deliveryPool), true);

        $builder->add($sub);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_basket_shipping';
    }
}
