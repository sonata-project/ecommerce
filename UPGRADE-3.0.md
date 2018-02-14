UPGRADE FROM 2.x to 3.0
=======================

## Deprecations

All the deprecated code introduced on 2.x is removed on 3.0.

Please read [2.x](https://github.com/sonata-project/ecommerce/tree/2.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/ecommerce/compare/2.x...3.0.0).

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