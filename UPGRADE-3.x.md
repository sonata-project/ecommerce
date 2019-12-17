UPGRADE 3.x
===========

UPGRADE FROM 3.0 to 3.1
=======================
 
## Fix products collection navigation

Product's collections are available on product page. The collection page
display a list of all similar products.

## Remove deprecated calls 

`Sonata\Doctrine\Model\PageableManagerInterface` is no longer used in profit of
`Sonata\DatagridBundle\Pager\PageableInterface`

## Added missing profile part to CustomerBundle

Profile dependencies are no longer fetched from SonataUserBundle, but you can
use this profile by changing the configuration (`template` and `menu_builder`). 

## Deprecated not passing dependencies to `Sonata\PaymentBundle\Controller\PaymentController`

Dependencies are no longer fetched from the container, so if you manually
instantiate that controller, you will need to pass arguments to it.
