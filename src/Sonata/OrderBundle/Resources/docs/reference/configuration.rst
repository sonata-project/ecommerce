
Full Configuration Options
--------------------------

.. code-block:: yaml

    sonata_order:
        class:
            order:          Application\Sonata\OrderBundle\Entity\Order
            order_element:  Application\Sonata\OrderBundle\Entity\OrderElement
            customer:       Application\Sonata\CustomerBundle\Entity\CustomerElement

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataOrderBundle: ~
                        SonataOrderBundle: ~
