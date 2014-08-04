.. index::
    single: Customer

========
Customer
========

Architecture
============

For more information about our position regarding the *customer* architecture, you can read: :doc:`../architecture/customer`.

Presentation
============

The ``SonataCustomerBundle`` basically manages the Customer-related entities & managers, offers ``AdminBundle`` integration and provides a basic controller and basic views to display the customers.
Moreover, the RecentCustomers admin dashboard block is present in this bundle as well.

Configuration
=============

The bundle allows you to configure the entity classes; you'll also need to register the Doctrine mapping.

.. code-block:: yaml

    sonata_customer:
        class:
            customer:             Application\Sonata\CustomerBundle\Entity\Customer
            address:              Application\Sonata\CustomerBundle\Entity\Address
            order:                Application\Sonata\OrderBundle\Entity\Order
            user:                 Application\Sonata\UserBundle\Entity\User

            # You can also implement custom components classes
            customer_selector:    Sonata\Component\Customer\CustomerSelector

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataCustomerBundle: ~
                        SonataCustomerBundle: ~
