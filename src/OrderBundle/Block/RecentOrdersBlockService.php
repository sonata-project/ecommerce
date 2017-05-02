<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Block;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @author Hugo Briand <briand@ekino.com>
 */
class RecentOrdersBlockService extends AbstractBlockService
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
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param string                   $name
     * @param EngineInterface          $templating
     * @param OrderManagerInterface    $orderManager
     * @param CustomerManagerInterface $customerManager
     * @param TokenStorageInterface    $tokenStorage
     * @param Pool                     $adminPool
     */
    public function __construct($name, EngineInterface $templating, OrderManagerInterface $orderManager, CustomerManagerInterface $customerManager, TokenStoraheInterface $tokenStorage, Pool $adminPool = null)
    {
        $this->orderManager = $orderManager;
        $this->customerManager = $customerManager;
        $this->tokenStorage = $tokenStorage;
        $this->adminPool = $adminPool;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = array();

        if ('admin' !== $blockContext->getSetting('mode')) {
            $orders = $this->orderManager->findForUser($this->tokenStorage->getToken()->getUser(), array('createdAt' => 'DESC'), $blockContext->getSetting('number'));
        } else {
            $orders = $this->orderManager->findBy($criteria, array('createdAt' => 'DESC'), $blockContext->getSetting('number'));
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'orders' => $orders,
            'admin_pool' => $this->adminPool,
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
                        'admin' => 'admin',
                    ),
                )),
            ),
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
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'number' => 5,
            'mode' => 'public',
            'title' => 'Recent Orders',
            'template' => 'SonataOrderBundle:Block:recent_orders.html.twig',
        ));
    }
}
