.. index::
    single: Customer

========
Customer
========

Presentation
============


The SonataCustomerBundle basically manages the Customer-related entities & managers, offers AdminBundle integration and provides a basic controller and basic views to display the customers.
Moreover, the RecentCustomers admin dashboard block is present in this bundle as well.


You may get more details about the architecture here: :doc:`../architecture/customer`.

Configuration
=============

The bundle allows you to configure the entity classes ; you'll also need to register the doctrine mapping.

.. code-block:: yaml
    :linenos:

    sonata_customer:
        class:
            customer:             Application\Sonata\CustomerBundle\Entity\Customer
            address:              Application\Sonata\CustomerBundle\Entity\Address
            order:                Application\Sonata\OrderBundle\Entity\Order
            user:                 Application\Sonata\UserBundle\Entity\User

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataCustomerBundle: ~
                        SonataCustomerBundle: ~
