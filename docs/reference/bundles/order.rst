.. index::
    single: Order
    single: OrderElement

=====
Order
=====

Architecture
============

For more information about our position regarding the *order* architecture, you can read: :doc:`../architecture/order`.

Presentation
============

The SonataOrderBundle basically manages the Order-related entities & managers, offers AdminBundle integration and provides a basic controller and basic views to display the orders.

Moreover, the RecentOrders admin & front dashboard block is present in this bundle as well.

Configuration
=============

The bundle allows you to configure the entity classes ; you'll also need to register the doctrine mapping.

.. code-block:: yaml

    sonata_order:
        class:
            order:                Application\Sonata\OrderBundle\Entity\Order
            order_element:        Application\Sonata\OrderBundle\Entity\OrderElement
            customer:             Application\Sonata\CustomerBundle\Entity\Customer

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataOrderBundle: ~
                        SonataOrderBundle: ~

