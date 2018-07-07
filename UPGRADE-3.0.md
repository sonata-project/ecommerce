UPGRADE FROM 2.x to 3.0
=======================

## Deprecations

All the deprecated code introduced on 2.x is removed on 3.0.

Please read [2.x](https://github.com/sonata-project/ecommerce/tree/2.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/ecommerce/compare/2.x...3.0.0).

## Controller changes

Some action signatures were changed, so that the `Request` is now injected into the action:

- `CustomerBundle\Controller\CustomerController::deleteAddressAction`

- `BasketBundle\Controller\BasketController::addProductAction`
- `BasketBundle\Controller\BasketController::paymentStepAction`
- `BasketBundle\Controller\BasketController::deliveryStepAction`
- `BasketBundle\Controller\BasketController::deliveryAddressStepAction`
- `BasketBundle\Controller\BasketController::paymentAddressStepAction`
- `BasketBundle\Controller\BasketController::finalReviewStepAction`
- `BasketBundle\Controller\BasketController::updateAction`

- `PaymentBundle\Controller\DebugPaymentController::paymentAction`
- `PaymentBundle\Controller\DebugPaymentController::processPaymentAction`
- `PaymentBundle\Controller\DebugPaymentController::checkRequest`
- `PaymentBundle\Controller\PaymentController::errorAction`
- `PaymentBundle\Controller\PaymentController::confirmationAction`
- `PaymentBundle\Controller\PaymentController::sendbankAction`
- `PaymentBundle\Controller\PaymentController::callbackAction`

- `PaymentBundle\ProductBundle\BaseProductController::variationToProductAction`
- `PaymentBundle\ProductBundle\CatalogController::indexAction`
- `PaymentBundle\ProductBundle\CatalogController::retrieveCategoryFromQueryString`
- `PaymentBundle\ProductBundle\CollectionController::indexAction`
- `PaymentBundle\ProductBundle\CollectionController::listSubCollectionsAction`
- `PaymentBundle\ProductBundle\CollectionController::listProductsAction`
- `PaymentBundle\ProductBundle\ProductController::getPriceStockForQuantityAction`
- `PaymentBundle\ProductBundle\ProductController::listProductsAction`

## Schema migrations

#### BaseProduct
- `price` field - `precision` changed from `10` to `20`

#### BaseOrder
- `totalInc` field - `precision` changed from `10` to `20`
- `totalExcl` field - `precision` changed from `10` to `20`
- `deliveryCost` field - `precision` changed from `10` to `20`

#### BaseOrderElement
- `unitPriceExcl` field - `precision` changed from `10` to `20`
- `unitPriceInc` field - `precision` changed from `10` to `20`
- `price` field - `precision` changed from `10` to `20`

#### BaseBasketElement
- Added missing `price_inc_vat` column for `priceIncludingVat` field

## CustomerSelector
If you have implemented custom `CustomerSelector` you must adapt constructor arguments to the new ones. Note that all protected properties are now private.

## ProductInterface
`ProductInterface::validateOneMainCategory` now uses `Symfony\Component\Validator\Context\ExecutionContextInterface` instead of `Symfony\Component\Validator\ExecutionContextInterface`

## RecentOrdersBlockService
`RecentOrdersBlockService::__construct()` last 2 arguments are changed:

- `Symfony\Component\Security\Core\SecurityContextInterface $securityContext` is replaced by `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface  $tokenStorage`
- `Sonata\AdminBundle\Admin\Pool $pool` is no longer `null` by default, pass `null` explicitly instead
