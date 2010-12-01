Installation
============

* Check dependencies are installed :

    * Symfony2
    * PHP 5.3.2

* add the DoctrineUserBundle bundle (user management) and follow DoctrineUserBundle README

        git submodule add git://github.com/knplabs/DoctrineUserBundle.git src/Bundle/DoctrineUserBundle

* add the EasyExtendsBundle bundle (user management) and follow EasyExtendsBundle README

        git submodule add git://github.com/sonata-project/EasyExtendsBundle.git src/Bundle/EasyExtendsBundle

* add the MediaBundle bundle (media management) and follow MediaBundle README

        git submodule add git://github.com/sonata-project/MediaBundle.git src/Bundle/MediaBundle


* add the following bundle in your kernel::registerBundles() method

        new Bundle\DoctrineUserBundle\DoctrineUserBundle(),
        new Bundle\EasyExtendsBundle\EasyExtendsBundle(),

        new Sonata\Bundle\ProductBundle\ProductBundle(),
        new Sonata\Bundle\BasketBundle\BasketBundle(),
        new Sonata\Bundle\OrderBundle\OrderBundle(),
        new Sonata\Bundle\InvoiceBundle\InvoiceBundle(),
        new Sonata\Bundle\MediaBundle\MediaBundle(),
        new Sonata\Bundle\DeliveryBundle\DeliveryBundle(),
        new Sonata\Bundle\PaymentBundle\PaymentBundle(),


* run the easy-extends:generate command, this command will generate the Application entities required by the Sonata's Bundles

        php yourproject/console easy-extends:generate

* then add the following bundles in your kernel::registerBundles() method

            new Application\DeliveryBundle\DeliveryBundle(),
            new Application\BasketBundle\BasketBundle(),
            new Application\InvoiceBundle\InvoiceBundle(),
            new Application\MediaBundle\MediaBundle(),
            new Application\OrderBundle\OrderBundle(),
            new Application\PaymentBundle\PaymentBundle(),
            new Application\ProductBundle\ProductBundle(),
            new Application\UrlShortenerBundle\UrlShortenerBundle(),


  You can use this bundle to extends entities or template files

* add the following dir mappings in your kernel::registerBundleDirs() method

        'Sonata\\Contrib'       => __DIR__.'/../src/sonata/src/Sonata/Contrib',
        'Sonata\\Bundle'        => __DIR__.'/../src/sonata/src/Sonata/Bundle',

* add the following autoload information into the autoload.php file

        'Sonata\\Contrib'       => __DIR__.'/../src/sonata/src/Sonata/Contrib',
        'Sonata\\Bundle'        => __DIR__.'/../src/sonata/src/Sonata/Bundle',
        'Imagine'               => __DIR__.'/vendor/lib',

* edit your config.yml and add the following lines

        sonata_delivery.config:
            pool: # all available delivery method
                class: Sonata\Component\Delivery\Pool
                methods:
                    - { id: free, name: Free, enabled: true, class: Sonata\Component\Delivery\FreeDelivery }

            selector:
                class: Sonata\Component\Delivery\Selector
            
        sonata_payment.config:
            methods:
                - { id: free, name: Free, enabled: true, class: Sonata\Component\Payment\Free }

        sonata_basket.config:
            class: Sonata\Component\Basket\Basket


        sonata_product.config:
            products:
                - { id: bottle, name: Bottle, enabled: true, class: Application\ProductBundle\Entity\Bottle }

            class:
                model:
                    user: Application\DoctrineUserBundle\Entity\User # you must define your own user class

        sonata_payment.config:
            methods:
                free:
                    name: Free
                    enabled: true
                    class: Sonata\Component\Payment\Free
                    transformers:
                        basket: sonata.transformer.basket
                        order: sonata.transformer.order
            selector:
                class: Sonata\Component\Payment\Selector
        
* add the current lines in your routing.yml files

        # sonata front controller
        sonata_user:
            resource: Sonata/Bundle/UserBundle/Resources/config/routing/user.xml
            prefix: /shop/user

        sonata_order:
            resource: Sonata/Bundle/OrderBundle/Resources/config/routing/order.xml
            prefix: /shop/user/invoice

        sonata_product:
            resource: Sonata/Bundle/ProductBundle/Resources/config/routing/product.xml
            prefix: /shop/product

        sonata_category:
            resource: Sonata/Bundle/ProductBundle/Resources/config/routing/category.xml
            prefix: /shop/category

        sonata_payment:
            resource: Sonata/Bundle/PaymentBundle/Resources/config/routing/payment.xml
            prefix: /shop/payment

        sonata_invoice:
            resource: Sonata/Bundle/InvoiceBundle/Resources/config/routing/invoice.xml
            prefix: /shop/user/invoice


        # sonata admin controller
        sonata_admin_product:
            resource: Sonata/Bundle/ProductBundle/Resources/config/routing/product_admin.xml
            prefix: /admin/shop/product

        sonata_admin_product:
            resource: Sonata/Bundle/ProductBundle/Resources/config/routing/category_admin.xml
            prefix: /admin/shop/category


        sonata_admin_order:
            resource: Sonata/Bundle/OrderBundle/Resources/config/routing/order_admin.xml
            prefix: /admin/shop/order

        sonata_admin_order:
            resource: Sonata/Bundle/InvoiceBundle/Resources/config/routing/invoice_admin.xml
            prefix: /admin/shop/invoice
