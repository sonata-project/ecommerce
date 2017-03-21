
Full Configuration Options
--------------------------

.. code-block:: yaml

    sonata_payment:
        services:
            paypal:
                name:     Paypal
                enabled:  true
                code:     paypal

                transformers:
                    basket: sonata.payment.transformer.basket
                    order:  sonata.payment.transformer.order

                options:
                    shop_secret_key:    assdsds
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation

                    web_connector_name: curl

                    account:            your_paypal_account@fake.com
                    cert_id:            fake
                    paypal_cert_file:   %kernel.root_dir%/paypal_cert_pem_sandbox.txt
                    url_action:         https://www.sandbox.paypal.com/cgi-bin/webscr

                    debug: true
                    class_order:        Application\Sonata\OrderBundle\Entity\Order
                    method:             encryptViaBuffer # encryptViaFile || encryptViaBuffer

                    key_file:           %kernel.root_dir%/my-prvkey.pem
                    cert_file:          %kernel.root_dir%/my-pubcert.pem

                    openssl:            /opt/local/bin/openssl

            pass:
                name:                   Pass
                enabled:                true
                code:                   pass
                browser:                sonata.payment.browser.curl

                transformers:
                    basket:             sonata.payment.transformer.basket
                    order:              sonata.payment.transformer.order

                options:
                    shop_secret_key:    assdsds
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation

            check:
                name:                   Check
                enabled:                true
                code:                   check
                browser:                sonata.payment.browser.curl

                transformers:
                    basket:             sonata.payment.transformer.basket
                    order:              sonata.payment.transformer.order

                options:
                    shop_secret_key:    assdsds
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation


            scellius:
                name:     Scellius
                enabled:  true
                code:     scellius

                transformers:
                    basket: sonata.payment.transformer.basket
                    order:  sonata.payment.transformer.order

                options:
                    url_callback:       sonata_payment_callback
                    url_return_ko:      sonata_payment_error
                    url_return_ok:      sonata_payment_confirmation
                    shop_secret_key:    Secret Key

                    request_command:    %scellius.request_command%
                    response_command:   %scellius.response_command%
                    merchant_id:        %scellius.merchant_id%
                    merchant_country:   fr
                    pathfile:           %scellius.pathfile%
                    language:           fr
                    payment_means:      %scellius.means%
                    header_fla:         no
                    data:

                    capture_day:
                    capture_mode:
                    bgcolor:
                    block_align:
                    block_order:
                    textcolor:

                    # Only available on pre production
                    normal_return_logo:
                    cancel_return_logo:
                    submit_logo:
                    logo_id:
                    logo_id2:
                    advert:
                    background_id:
                    templatefile:


        # service which find the correct payment methods for a basket
        selector: sonata.payment.selector.simple

        # service which generate the correct order and invoice number
        generator: sonata.payment.generator.mysql # or sonata.payment.generator.postgres

        transformers:
            order:  sonata.payment.transformer.order
            basket: sonata.payment.transformer.basket

        class:
            order:          Application\Sonata\OrderBundle\Entity\Order
            transaction:    Application\Sonata\PaymentBundle\Entity\Transaction

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataPaymentBundle: ~
                        SonataPaymentBundle: ~
