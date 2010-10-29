Installation
============

* Check dependencies are installed :

    * Symfony2
    * PHP 5.3.2

* add the following bundle in your kernel::registerBundles() method

        new Sonata\Bundle\ProductBundle\ProductBundle(),
        new Sonata\Bundle\BasketBundle\BasketBundle(),
        new Sonata\Bundle\DeliveryBundle\DeliveryBundle(),
        new Sonata\Bundle\PaymentBundle\PaymentBundle(),

* add the following dir mappings in your kernel::registerBundleDirs() method

        'Sonata\\Contrib'       => __DIR__.'/../src/sonata/src/Sonata/Contrib',
        'Sonata\\Bundle'        => __DIR__.'/../src/sonata/src/Sonata/Bundle',


* add the following autoload information into the autoload.php file

        'Sonata\\Contrib'       => __DIR__.'/../src/sonata/src/Sonata/Contrib',
        'Sonata\\Bundle'        => __DIR__.'/../src/sonata/src/Sonata/Bundle',


* edit your config.yml and add the following lines

        sonata_delivery.config:
            methods:
                - { id: free, name: Free, enabled: true, class: Sonata\Component\Delivery\Free }

        sonata_payment.config:
            methods:
                - { id: free, name: Free, enabled: true, class: Sonata\Component\Payment\Free }

        sonata_basket.config:
            class: Sonata\Component\Basket\Basket

        sonata_products.config:
            methods:
                - { id: free, name: Free, enabled: true, class: Sonata\Component\Payment\Free }

