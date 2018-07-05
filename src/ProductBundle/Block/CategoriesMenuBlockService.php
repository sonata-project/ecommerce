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

namespace Sonata\ProductBundle\Block;

use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\MenuBlockService;
use Sonata\ProductBundle\Menu\ProductMenuBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CategoriesMenuBlockService extends MenuBlockService
{
    /**
     * @var ProductMenuBuilder
     */
    private $menuBuilder;

    /**
     * @param string                $name
     * @param EngineInterface       $templating
     * @param MenuProviderInterface $menuProvider
     * @param ProductMenuBuilder    $menuBuilder
     */
    public function __construct($name, EngineInterface $templating, MenuProviderInterface $menuProvider, ProductMenuBuilder $menuBuilder)
    {
        parent::__construct($name, $templating, $menuProvider, []);

        $this->menuBuilder = $menuBuilder;
    }

    public function getName()
    {
        return 'Categories Menu';
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults([
                'menu_template' => 'SonataBlockBundle:Block:block_side_menu_template.html.twig',
                'safe_labels' => true,
            ]);
    }

    protected function getMenu(BlockContextInterface $blockContext)
    {
        $settings = $blockContext->getSettings();

        $menu = parent::getMenu($blockContext);

        if (null === $menu || '' === $menu) {
            $menu = $this->menuBuilder->createCategoryMenu(
                [
                    'childrenAttributes' => ['class' => $settings['menu_class']],
                    'attributes' => ['class' => $settings['children_class']],
                ],
                $settings['current_uri']
            );
        }

        return $menu;
    }
}
