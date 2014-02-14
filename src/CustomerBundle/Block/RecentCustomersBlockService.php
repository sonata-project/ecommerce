<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RecentCustomersBlockService
 *
 * @package Sonata\CustomerBundle\Block
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class RecentCustomersBlockService extends BaseBlockService
{
    protected $manager;

    /**
     * @param string                   $name
     * @param EngineInterface          $templating
     * @param CustomerManagerInterface $manager
     */
    public function __construct($name, EngineInterface $templating, CustomerManagerInterface $manager)
    {
        $this->manager = $manager;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = array(
//            'mode' => $blockContext->getSetting('mode')
        );

        return $this->renderResponse($blockContext->getTemplate(), array(
            'context'   => $blockContext,
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
            'customers' => $this->manager->findBy($criteria, array('createdAt' => 'DESC'), $blockContext->getSetting('number'))
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('number', 'integer', array('required' => true)),
                array('title', 'text', array('required' => false)),
                array('mode', 'choice', array(
                    'choices' => array(
                        'public' => 'public',
                        'admin'  => 'admin'
                    )
                ))
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Recent Customers';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'number'     => 5,
            'mode'       => 'public',
            'title'      => 'Recent Customers',
//            'tags'      => 'Recent Customers',
            'template'   => 'SonataCustomerBundle:Block:recent_customers.html.twig'
        ));
    }
}
