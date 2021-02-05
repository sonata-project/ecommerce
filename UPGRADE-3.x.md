UPGRADE 3.x
===========

UPGRADE FROM 3.4 to 3.5
=======================

### Dependencies

Controllers for NelmioApiDocBundle v2 were moved under `Sonata\UserBundle\Controller\Api\Legacy\` namespace and controllers for NelmioApiDocBundle v3 were added as replacement. If you extend them, you must ensure they are using the corresponding inheritance.	- "sonata-project/datagrid-bundle" is bumped from ^2.4 to ^3.0.

  If you are extending these method you MUST add argument and return type declarations:
  - `Sonata\Component\Basket\BasketManager::getPager()`
  - `Sonata\Component\Customer\AddressManager::getPager()`
  - `Sonata\CustomerBundle\Entity\CustomerManager::getPager()`
  - `Sonata\InvoiceBundle\Entity\InvoiceManager::getPager()`
  - `Sonata\OrderBundle\Entity\OrderManager::getPager()`
  - `Sonata\ProductBundle\Entity\ProductManager::getPager()`

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

## Fix products collection navigation
Product's collections are available on product page. The collection page display a list of all similar products.

## Remove deprecated calls
`Sonata\Doctrine\Model\PageableManagerInterface` is no longer used in profit of `Sonata\DatagridBundle\Pager\PageableInterface`

## Deprecated not passing dependencies to `Sonata\PaymentBundle\Controller\PaymentController`

Dependencies are no longer fetched from the container, so if you manually
instantiate that controller, you will need to pass arguments to it.
