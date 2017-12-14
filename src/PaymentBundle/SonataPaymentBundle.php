<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Sonata\PaymentBundle\Form\Type\PaymentTransactionStatusType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataPaymentBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping([
            'sonata_payment_transaction_status' => PaymentTransactionStatusType::class,
        ]);
    }
}
