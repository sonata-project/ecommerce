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
