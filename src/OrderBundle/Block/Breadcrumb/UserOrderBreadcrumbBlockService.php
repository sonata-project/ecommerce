<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\UserBundle\Block\Breadcrumb\BaseUserProfileBreadcrumbBlockService;

/**
 * Class for user breadcrumbs.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class UserOrderBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.order.block.breadcrumb_order';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_order_user_breadcrumb', array(
            'route'  => 'sonata_order_index',
            'extras' => array('translation_domain' => 'SonataOrderBundle')
        ));

        return $menu;
    }
}
