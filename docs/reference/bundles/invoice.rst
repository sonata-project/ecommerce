.. index::
    single: Invoice

=======
Invoice
=======

Architecture
============

For more information about our position regarding the *invoice* architecture, you can read: :doc:`../architecture/invoice`.

Presentation
============

The ``SonataInvoiceBundle`` manages everything related to the invoices: it basically implements the `Invoice` components by offering DB entities, and an Admin page to view the invoices in the Back end. It also offers the possibility to view a generated Invoice from the front office with a specific template.

The `Invoice` generation, however, is not processed by this specific bundle, but by the ``PaymentBundle``, as are all other entity transformations.

Configuration
=============

The bundle allows you to configure the entity classes; you'll also need to register the Doctrine mapping.

.. code-block:: yaml

    sonata_invoice:
        class:
            invoice:              Application\Sonata\InvoiceBundle\Entity\Invoice
            invoice_element:      Application\Sonata\InvoiceBundle\Entity\InvoiceElement
            order_element:        Application\Sonata\OrderBundle\Entity\OrderElement
            customer:             Application\Sonata\CustomerBundle\Entity\Customer

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataInvoiceBundle: ~
                        SonataInvoiceBundle: ~

Transformer
===========

Default `Order` to `Invoice` transformer is provided in ``Sonata\Component\Transformer\InvoiceTransformer::transformFromOrder``; service id is ``sonata.payment.transformer.invoice`` and you can override it by setting its class name parameter (``sonata.invoice_transformer.class``).

Currently, a raw HTML representation for invoices is provided; would you like to generate a PDF ? We encourage you to check out PDF generation bundles taking HTML as inputs.
The invoice rendering template is ``SonataInvoiceBundle:Invoice:view.html.twig``; you can override it by using Symfony bundle override rules.
