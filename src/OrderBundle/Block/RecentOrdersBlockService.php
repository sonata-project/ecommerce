<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class RecentOrdersBlockService
 *
 * @package Sonata\OrderBundle\Block
 *
 * @author  Thomas Rabaix
 * @author  Hugo Briand <briand@ekino.com>
 */
class RecentOrdersBlockService extends BaseBlockService
{
    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param string                   $name
     * @param EngineInterface          $templating
     * @param OrderManagerInterface    $orderManager
     * @param CustomerManagerInterface $customerManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct($name, EngineInterface $templating, OrderManagerInterface $orderManager, CustomerManagerInterface $customerManager, SecurityContextInterface $securityContext)
    {
        $this->orderManager    = $orderManager;
        $this->customerManager = $customerManager;
        $this->securityContext = $securityContext;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = array();

        if ('admin' !== $blockContext->getSetting('mode')) {
            $orders = $this->orderManager->findForUser($this->securityContext->getToken()->getUser(), array('createdAt' => 'DESC'), $blockContext->getSetting('number'));
        } else {
            $orders = $this->orderManager->findBy($criteria, array('createdAt' => 'DESC'), $blockContext->getSetting('number'));
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'context'   => $blockContext,
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
            'orders'    => $orders
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
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
        return 'Recent Orders';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'number'     => 5,
            'mode'       => 'public',
            'title'      => 'Recent Orders',
            'template'   => 'SonataOrderBundle:Block:recent_orders.html.twig'
        ));
    }
}
