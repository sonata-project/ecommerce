UPGRADE 3.x
===========

UPGRADE FROM 3.0 to 3.1
=======================

## Added missing profile part to CustomerBundle

Dashboard from SonataUserBundle was drop in 4.x. To keep functionality 
profile was copy to CustomerBundle. Template and menu_builder was added 
to configuration for easy extends/override this profile.

## Deprecated not passing dependencies to `Sonata\PaymentBundle\Controller\PaymentController`

Dependencies are no longer fetched from the container, so if you manually
instantiate that controller, you will need to pass arguments to it.
