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

namespace Sonata\BasketBundle\Form;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Form\Transformer\PaymentMethodTransformer;
use Sonata\Component\Payment\PaymentSelectorInterface;
use Sonata\Component\Payment\Pool as PaymentPool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentType extends AbstractType
{
    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var PaymentPool
     */
    protected $paymentPool;

    /**
     * @var PaymentSelectorInterface
     */
    protected $paymentSelector;

    /**
     * @param AddressManagerInterface  $addressManager
     * @param PaymentPool              $paymentPool
     * @param PaymentSelectorInterface $paymentSelector
     */
    public function __construct(AddressManagerInterface $addressManager, PaymentPool $paymentPool, PaymentSelectorInterface $paymentSelector)
    {
        $this->addressManager = $addressManager;
        $this->paymentSelector = $paymentSelector;
        $this->paymentPool = $paymentPool;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        $addresses = $this->addressManager->findBy([
            'customer' => $basket->getCustomer()->getId(),
            'type' => AddressInterface::TYPE_BILLING,
        ]);

        /*
         * TODO: implement billing address choice
        $builder->add('billingAddress', 'entity', array(
            'class' => $this->addressManager->getClass(),
            'choices' => $addresses,
            'expanded' => true,
        ));

         */
        $address = $basket->getBillingAddress() ?: current($addresses);
        $basket->setBillingAddress($address ?: null);

        $methods = $this->paymentSelector->getAvailableMethods($basket, $basket->getDeliveryAddress());

        $choices = [];
        foreach ($methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        reset($methods);

        $method = $basket->getPaymentMethod() ?: current($methods);
        $basket->setPaymentMethod($method ?: null);

        $sub = $builder->create('paymentMethod', ChoiceType::class, [
            'expanded' => true,
            'choice_list' => new SimpleChoiceList($choices),
        ]);

        $sub->addViewTransformer(new PaymentMethodTransformer($this->paymentPool), true);

        $builder->add($sub);
    }

    public function getBlockPrefix()
    {
        return 'sonata_basket_payment';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
