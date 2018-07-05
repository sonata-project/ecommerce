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

namespace Sonata\CustomerBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\UserBundle\Block\Breadcrumb\BaseUserProfileBreadcrumbBlockService;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class CustomerAddressBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    public function getName()
    {
        return 'sonata.customer.block.breadcrumb_address';
    }

    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_customer_addresses_breadcrumb', [
            'route' => 'sonata_customer_addresses',
            'extras' => ['translation_domain' => 'SonataCustomerBundle'],
        ]);

        return $menu;
    }
}
