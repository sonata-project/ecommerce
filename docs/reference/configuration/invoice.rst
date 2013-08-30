=======
Invoice
=======

Default order to invoice transformer is provided in ``Sonata\Component\Transformer\InvoiceTransformer::transformFromOrder`` ; service id is ``sonata.payment.transformer.invoice`` and you can override it by setting its class name parameter (``sonata.invoice_transformer.class``).

Currently, a raw HTML representation for invoices is provided ; would you like to generate a PDF, we encourage you to check out PDF generation bundles taking HTML as inputs.
The invoice rendering template is ``SonataInvoiceBundle:Invoice:view.html.twig`` ; you can override it by using Symfony bundle override rules.
