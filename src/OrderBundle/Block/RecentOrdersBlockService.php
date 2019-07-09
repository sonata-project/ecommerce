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

namespace Sonata\OrderBundle\Block;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @author Hugo Briand <briand@ekino.com>
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
     * @param string $name
     * @param Pool   $adminPool
     */
    public function __construct($name, EngineInterface $templating, OrderManagerInterface $orderManager, CustomerManagerInterface $customerManager, SecurityContextInterface $securityContext, Pool $adminPool = null)
    {
        $this->orderManager = $orderManager;
        $this->customerManager = $customerManager;
        $this->securityContext = $securityContext;
        $this->adminPool = $adminPool;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = [];

        if ('admin' !== $blockContext->getSetting('mode')) {
            $orders = $this->orderManager->findForUser($this->securityContext->getToken()->getUser(), ['createdAt' => 'DESC'], $blockContext->getSetting('number'));
        } else {
            $orders = $this->orderManager->findBy($criteria, ['createdAt' => 'DESC'], $blockContext->getSetting('number'));
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'orders' => $orders,
            'admin_pool' => $this->adminPool,
        ], $response);
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
        $formMapper->add('settings', 'sonata_type_immutable_array', [
            'keys' => [
                ['number', 'integer', ['required' => true]],
                ['title', 'text', ['required' => false]],
                ['mode', 'choice', [
                    'choices' => [
                        'public' => 'public',
                        'admin' => 'admin',
                    ],
                ]],
            ],
        ]);
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
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => 'Recent Orders',
            'template' => 'SonataOrderBundle:Block:recent_orders.html.twig',
        ]);
    }
}
