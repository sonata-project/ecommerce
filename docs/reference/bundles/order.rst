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

The ``SonataOrderBundle`` basically manages the Order-related entities & managers, offers AdminBundle integration and provides a basic controller and basic views to display the orders.

Moreover, the RecentOrders admin & front dashboard block is present in this bundle as well.

Tools
=====

Status Renderer
---------------

The ``SonataOrderBundle`` provides a status renderer for the order and orderelement objects, which handles the object's status as well as payment and delivery statuses.

The `Status Renderer` twig helper is documented in `SonataCoreBundle <http://sonata-project.org/bundles/core/master/doc/reference/status_helper.html>`_.

Block Services
--------------

A `Recent Orders` block service is available, that can be contextualised to a user or the admin (you've got implementation samples in the demo). You can customize the number of orders displayed.

You also have a `Breadcrumb` block service, see `SonataSeoBundle documentation <http://sonata-project.org/bundles/seo/master/doc/reference/breadcrumb.html>`_.

Configuration
=============

The bundle allows you to configure the entity classes; you'll also need to register the Doctrine mapping.

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

