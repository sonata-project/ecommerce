.. index::
    single: Invoice

=======
Invoice
=======

Presentation
============

The InvoiceBundle manages everything related to the invoices: it basically implements the Invoice components by offering DB entities, and an Admin page to view the invoices in the BO. It also offers the possibility to view a generated Invoice from the front office with a specific template.

The Invoice generation, however, is not processed by this specific bundle, but by the PaymentBundle, as are all other entity transformations.

You may get more details about the architecture here: :doc:`../architecture/invoice`.

Configuration
=============

Default order to invoice transformer is provided in ``Sonata\Component\Transformer\InvoiceTransformer::transformFromOrder`` ; service id is ``sonata.payment.transformer.invoice`` and you can override it by setting its class name parameter (``sonata.invoice_transformer.class``).

Currently, a raw HTML representation for invoices is provided ; would you like to generate a PDF ? We encourage you to check out PDF generation bundles taking HTML as inputs.
The invoice rendering template is ``SonataInvoiceBundle:Invoice:view.html.twig`` ; you can override it by using Symfony bundle override rules.