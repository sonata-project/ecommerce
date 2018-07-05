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
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Delivery\ServiceDeliverySelectorInterface;
use Sonata\Component\Delivery\UndeliverableCountryException;
use Sonata\Component\Form\Transformer\DeliveryMethodTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingType extends AbstractType
{
    /**
     * @var DeliveryPool
     */
    protected $deliveryPool;

    /**
     * @var ServiceDeliverySelectorInterface
     */
    protected $deliverySelector;

    /**
     * @param DeliveryPool                     $deliveryPool
     * @param ServiceDeliverySelectorInterface $deliverySelector
     */
    public function __construct(DeliveryPool $deliveryPool, ServiceDeliverySelectorInterface $deliverySelector)
    {
        $this->deliverySelector = $deliverySelector;
        $this->deliveryPool = $deliveryPool;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RuntimeException('Please provide a BasketInterface instance');
        }

        $methods = $this->deliverySelector->getAvailableMethods($basket, $basket->getDeliveryAddress());

        if (0 === count($methods)) {
            throw new UndeliverableCountryException($basket->getDeliveryAddress());
        }

        $choices = [];
        foreach ($methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        reset($methods);

        $method = $basket->getDeliveryMethod() ?: current($methods);
        $basket->setDeliveryMethod($method ?: null);

        $sub = $builder->create('deliveryMethod', ChoiceType::class, [
            'expanded' => true,
            'choice_list' => new SimpleChoiceList($choices),
        ]);

        $sub->addViewTransformer(new DeliveryMethodTransformer($this->deliveryPool), true);

        $builder->add($sub);
    }

    public function getBlockPrefix()
    {
        return 'sonata_basket_shipping';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
