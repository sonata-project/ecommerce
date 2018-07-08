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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Pool|null
     */
    protected $adminPool;

    public function __construct(
        string $name,
        EngineInterface $templating,
        OrderManagerInterface $orderManager,
        CustomerManagerInterface $customerManager,
        TokenStorageInterface $tokenStorage,
        ?Pool $adminPool
    ) {
        $this->orderManager = $orderManager;
        $this->customerManager = $customerManager;
        $this->tokenStorage = $tokenStorage;
        $this->adminPool = $adminPool;

        parent::__construct($name, $templating);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        if ('admin' !== $blockContext->getSetting('mode')) {
            $orders = $this->orderManager->findForUser(
                $this->tokenStorage->getToken()->getUser(),
                ['createdAt' => 'DESC'],
                $blockContext->getSetting('number')
            );
        } else {
            $orders = $this->orderManager->findBy(
                [],
                ['createdAt' => 'DESC'],
                $blockContext->getSetting('number')
            );
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'orders' => $orders,
            'admin_pool' => $this->adminPool,
        ], $response);
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block): void
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

    public function getName()
    {
        return 'Recent Orders';
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => 'Recent Orders',
            'template' => 'SonataOrderBundle:Block:recent_orders.html.twig',
        ]);
    }
}
