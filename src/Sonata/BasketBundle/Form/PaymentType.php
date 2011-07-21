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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Form\Transformer\PaymentMethodTransformer;

use Sonata\Component\Payment\PaymentSelectorInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;
use Sonata\AdminBundle\Model\ModelManagerInterface;

class PaymentType extends AbstractType
{
    protected $addressManager;

    protected $paymentPool;

    protected $paymentSelector;

    protected $modelManager;

    public function __construct(AddressManagerInterface $addressManager, ModelManagerInterface $modelManager, PaymentPool $paymentPool, PaymentSelectorInterface $paymentSelector)
    {
        $this->addressManager   = $addressManager;
        $this->modelManager     = $modelManager;
        $this->paymentSelector  = $paymentSelector;
        $this->paymentPool      = $paymentPool;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        $addresses = $this->addressManager->findBy(array(
            'customer' => $basket->getCustomer()->getId(),
            'type'     => AddressInterface::TYPE_BILLING
        ));

        $builder->add('paymentAddress', 'sonata_type_model', array(
            'model_manager' => $this->modelManager,
            'class'         => $this->addressManager->getClass(),
            'choices'       => $addresses,
            'expanded'      => true
        ));

        $address = $basket->getPaymentAddress() ?: current($addresses);
        $basket->setPaymentAddress($address ?: null);

        $methods = $this->paymentSelector->getAvailableMethods($basket, $basket->getDeliveryAddress());

        $choices = array();
        foreach ($methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        reset($methods);

        $method = $basket->getPaymentMethod() ?: current($methods);
        $basket->setPaymentMethod($method ?: null);

        $sub = $builder->create('paymentMethod', 'choice', array(
            'expanded'     => true,
            'choice_list'  => new ArrayChoiceList($choices),
        ));

        $sub->prependClientTransformer(new PaymentMethodTransformer($this->paymentPool));

        $builder->add($sub);
    }
}
