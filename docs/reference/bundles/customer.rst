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
        profile:
            template:       '@SonataCustomer/Profile/action.html.twig'
            menu_builder:   'sonata.customer.profile.menu_builder.default'

            menu:
                -
                    route: 'sonata_customer_dashboard'
                    label: 'link_list_dashboard'
                    domain: 'SonataCustomerBundle'
                    route_parameters: {}
                -
                    route: 'sonata_customer_addresses'
                    label: 'link_list_addresses'
                    domain: 'SonataCustomerBundle'
                    route_parameters: {}
                -
                    route: 'sonata_order_index'
                    label: 'order_list'
                    domain: 'SonataOrderBundle'
                    route_parameters: {}

            blocks:
                -
                    position: left
                    type: sonata.order.block.recent_orders
                    settings: { title: Recent Orders, number: 5, mode: public }
                -
                    position: right
                    type: sonata.news.block.recent_posts
                    settings: { title: Recent Posts, number: 5, mode: public }
                -
                    position: right
                    type: sonata.news.block.recent_comments
                    settings: { title: Recent Comments, number: 5, mode: public }

        class:
            customer:             App\Sonata\CustomerBundle\Entity\Customer
            address:              App\Sonata\CustomerBundle\Entity\Address
            order:                App\Sonata\OrderBundle\Entity\Order
            user:                 App\Sonata\UserBundle\Entity\User

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
