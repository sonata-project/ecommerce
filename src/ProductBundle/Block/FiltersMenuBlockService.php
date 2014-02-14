<?php
/*
 * This file is part of the Sonata package.
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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Templating\EngineInterface;


/**
 * Class FiltersMenuBlockService
 *
 * @package Sonata\ProductBundle\Block
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FiltersMenuBlockService extends MenuBlockService
{
    /**
     * @var ProductMenuBuilder
     */
    private $menuBuilder;

    /**
     * Constructor
     *
     * @param string                $name
     * @param EngineInterface       $templating
     * @param MenuProviderInterface $menuProvider
     * @param ProductMenuBuilder    $menuBuilder
     */
    public function __construct($name, EngineInterface $templating, MenuProviderInterface $menuProvider, ProductMenuBuilder $menuBuilder)
    {
        parent::__construct($name, $templating, $menuProvider, array());

        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Filters Menu';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        parent::setDefaultSettings($resolver);

        $resolver->setDefaults(array(
            'menu_class'       => "nav nav-list",
            'product_provider' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormSettingsKeys()
    {
        return array_merge(parent::getFormSettingsKeys(), array(
            array('menu_class', 'text', array('required' => false)),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $settings = $blockContext->getSettings();

        $menu = parent::getMenu($blockContext);

        if (null === $menu || "" === $menu) {
            $menu = $this->menuBuilder->createFiltersMenu($settings['product_provider'], array('childrenAttributes' => array('class' => $settings['menu_class'])), $settings['current_uri']);
        }

        return $menu;
    }

}
