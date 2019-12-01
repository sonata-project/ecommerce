UPGRADE 3.x
===========

UPGRADE FROM 3.0 to 3.1
=======================

## Deprecated not passing dependencies to `Sonata\PaymentBundle\Controller\PaymentController`

Dependencies are no longer fetched from the container, so if you manually
instantiate that controller, you will need to pass arguments to it.
