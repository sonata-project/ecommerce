<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Vincent Composieux <composieux@ekino.com>
 *
 * Address form type (used for customer addresses add/edit actions)
 */
class AddressType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $getter;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor
     *
     * @param string $class  A class to apply getter
     * @param string $getter A getter method name
     * @param string $name   A form type name
     */
    public function __construct($class, $getter, $name)
    {
        $this->class  = $class;
        $this->getter = $getter;
        $this->name   = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'sonata_basket_address';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'types' => call_user_func(array($this->class, $this->getter))
        ));
    }
}
