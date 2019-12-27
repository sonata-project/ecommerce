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

namespace Sonata\ProfileBundle\Block;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\MenuBlockService;
use Sonata\ProfileBundle\Menu\ProfileMenuBuilder;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class ProfileMenuBlockService extends MenuBlockService
{
    /**
     * @var ProfileMenuBuilder
     */
    private $menuBuilder;

    public function __construct(string $name, EngineInterface $templating, MenuProviderInterface $menuProvider, ProfileMenuBuilder $menuBuilder)
    {
        parent::__construct($name, $templating, $menuProvider, []);

        $this->menuBuilder = $menuBuilder;
    }

    public function getName()
    {
        return 'Ecommerce Profile Menu';
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults([
            'cache_policy' => 'private',
            'menu_template' => 'SonataBlockBundle:Block:block_side_menu_template.html.twig',
        ]);
    }

    /**
     * @return ItemInterface
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $settings = $blockContext->getSettings();

        $menu = parent::getMenu($blockContext);

        if (null === $menu || '' === $menu) {
            $menu = $this->menuBuilder->createProfileMenu(
                [
                    'childrenAttributes' => ['class' => $settings['menu_class']],
                    'attributes' => ['class' => $settings['children_class']],
                ]
            );

            if (method_exists($menu, 'setCurrentUri')) {
                $menu->setCurrentUri($settings['current_uri']);
            }
        }

        return $menu;
    }
}
