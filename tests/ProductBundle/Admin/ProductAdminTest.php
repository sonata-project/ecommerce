<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Admin;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Admin\ProductAdmin;
use Sonata\ProductBundle\Entity\BaseProduct;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class ProductAdminTest extends TestCase
{
    /** @var ProductAdmin */
    private $productAdmin;

    protected function setUp(): void
    {
        $this->productAdmin = new ProductAdmin(
            null,
            BaseProduct::class,
            'SonataProductBundle:ProductAdmin'
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation The Sonata\ProductBundle\Admin\ProductAdmin::getProductClass method is deprecated since version 2.2 and will be removed in 3.0.
     */
    public function testGetProductClassIsDeprecated(): void
    {
        $this->productAdmin->getProductClass();
    }
}
