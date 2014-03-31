.. index::
    single: Payment

=======
Payment
=======

At this time, several payment methods are handled in Sonata e-commerce. Whether you'd like to use credit card payment providers such as Scellius or Ogone ; simply Paypal ; or even want the ability to handle check payments, you should find what you need in what's already provided. Otherwise, you may of course add your own payment methods (and feel free to submit them to the community).

You may get more details about the architecture here: :doc:`../../architecture/payment`.

Methods
=======

.. toctree::
    :maxdepth: 2

    Scellius (Credit Card) <scellius>
    Ogone (Credit Card) <ogone>
    Paypal <paypal>
    Check <check>
    Pass <pass>
    Debug (dev environment only) <debug>

Add a payment method
====================

A payment method is basically a service that implements the ``PaymentInterface`` and that is tagged ``sonata.payment.method``. Thoses are the pre-requisites.
An abstract ``BasePayment`` class is available, we advise you to use it to implement your own payment method.

Once you've declared your service (for the example, let's say I've named it ``acme.payment.mymethod``), add it under the sonata_payment configuration as follows:

.. code-block:: yaml

    sonata_payment:
        services:
            # ...
            acme.payment.mymethod: ~

Please keep in mind that we won't process your service configuration. You'll need to call the following methods on your service (if you wish to) manually:

* ``setName``
* ``setCode``
* ``setEnabled``
* ``setOptions``
* ``addTransformer``

Your service must return a unique, non-null key when the ``getCode`` method is called, or it might be overridden in the payment methods pool. To do that, either set the $this->code parameter in the constructor, or override the ``getCode`` method to return a constant string as follows:

.. code-block:: php

    namespace Application\Sonata\PaymentBundle\Method;

    use Sonata\Component\Payment\BasePayment;

    class MyMethod extends BasePayment
    {
        public function __construct(/* ... */)
        {
            $this->setCode('mymethod');

            // ...
        }

        // or...

        public function getCode()
        {
            return 'mymethod';
        }
    }


Configuration
=============

Here's the full default configuration for SonataPaymentBundle:

.. code-block:: yaml

    sonata_payment:
        selector:             sonata.payment.selector.simple
        generator:            sonata.payment.generator.mysql
        transformers:
            order:                sonata.payment.transformer.order      # The service to transform an order into a basket
            basket:               sonata.payment.transformer.basket     # The service to transform a basket into an order
        services:
            # Which payment methods are enabled?
            paypal:
                name:                 Paypal
                code:                 paypal
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                options:
                    shop_secret_key:      ~
                    web_connector_name:   curl
                    account:              your_paypal_account@fake.com
                    cert_id:              fake
                    debug:                false
                    paypal_cert_file:     %kernel.root_dir%/paypal_cert_pem_sandbox.txt
                    url_action:           https://www.sandbox.paypal.com/cgi-bin/webscr
                    class_order:          Application\Sonata\OrderBundle\Entity\Order
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
                    method:               encryptViaBuffer
                    key_file:             %kernel.root_dir%/my-prvkey.pem
                    cert_file:            %kernel.root_dir%/my-pubcert.pem
                    openssl:              /opt/local/bin/openssl
            pass:
                name:                 Pass
                code:                 pass
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                browser:              sonata.payment.browser.curl
                options:
                    shop_secret_key:      ~
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
            check:
                name:                 Check
                code:                 check
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                browser:              sonata.payment.browser.curl
                options:
                    shop_secret_key:      ~
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
            scellius:
                name:                 Scellius
                code:                 scellius
                generator:            sonata.payment.provider.scellius.none_generator
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                options:
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
                    template:             SonataPaymentBundle:Payment:scellius.html.twig
                    shop_secret_key:      ~
                    request_command:      ~
                    response_command:     ~
                    merchant_id:          ~
                    merchant_country:     ~
                    pathfile:             ~
                    language:             ~
                    payment_means:        ~
                    base_folder:          ~
                    data:
                    header_flag:          no
                    capture_day:
                    capture_mode:
                    bgcolor:
                    block_align:
                    block_order:
                    textcolor:
                    normal_return_logo:
                    cancel_return_logo:
                    submit_logo:
                    logo_id:
                    logo_id2:
                    advert:
                    background_id:
                    templatefile:
            ogone:
                name:                 Ogone
                code:                 ogone
                transformers:
                    basket:               sonata.payment.transformer.basket
                    order:                sonata.payment.transformer.order
                options:
                    url_callback:         sonata_payment_callback
                    url_return_ko:        sonata_payment_error
                    url_return_ok:        sonata_payment_confirmation
                    form_url:             ~ # Required
                    catalog_url:          ~ # Required
                    home_url:             ~ # Required
                    pspid:                ~ # Required
                    sha_key:              ~ # Required
                    sha-out_key:          ~ # Required
                    template:             SonataPaymentBundle:Payment:ogone.html.twig
        class:
            order:                Application\Sonata\OrderBundle\Entity\Order
            transaction:          Application\Sonata\PaymentBundle\Entity\Transaction

        # Here you will enable the payment methods you wish to provide
        # and add your custom ones
        methods:
            pass: ~     # This is a provided method, we don't need to specify its service id
            bitcoin: application.acme.payment.bitcoin    # Custom payment method, we specify the service id


If you want to use the ``DebugPayment`` method, you need to add its configuration in the ``dev`` config file.

.. code-block:: yaml

    sonata_payment:
        services:
            debug:
                name:    Debug Payment
                enabled: true
                code:    debug
                browser: sonata.payment.browser.curl

                transformers:
                    basket: sonata.payment.transformer.basket
                    order:  sonata.payment.transformer.order

                options:
                    url_callback:  sonata_payment_callback
                    url_return_ko: sonata_payment_error
                    url_return_ok: sonata_payment_confirmation


Add a custom payment method
===========================

In order to add a custom payment methods, here are the steps to follow:

1. Create your own payment method class:

.. code-block:: php

    <?php

    namespace Application\AcmeBundle\Payment;

    use Sonata\Component\Payment\BasePayment;

    // ...

    /**
     * Class TakeAwayDelivery
     */
    class Bitcoin extends BasePayment
    {
        // ...

        /**
         * {@inheritdoc}
         */
        public function getCode()
        {
            return 'bitcoin';
        }

    }

2. Declare the service associated (don't forget the tag):

.. code-block:: xml

        <service id="application.acme.payment.bitcoin" class="Application\AcmeBundle\Payment\BitcoinPayment">
            <tag name="sonata.delivery.method" />
        </service>

3. Add it to your configuration:

.. code-block:: yaml

    sonata_payment:
        # ...

        methods:
            # ...
            bitcoin: application.acme.payment.bitcoin

4. That's it!