<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Form;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BasketValidator extends ConstraintValidator
{
    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var ConstraintValidatorFactory
     */
    protected $constraintValidatorFactory;

    /**
     * Constructor.
     *
     * @param ProductPool                $productPool
     * @param ConstraintValidatorFactory $constraintValidatorFactory
     */
    public function __construct(ProductPool $productPool, ConstraintValidatorFactory $constraintValidatorFactory)
    {
        $this->productPool  = $productPool;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
    }

    /**
     * The validator asks each product repository to validate the related basket element.
     *
     * @param BasketInterface $basket
     * @param Constraint      $constraint
     */
    public function validate($basket, Constraint $constraint)
    {
        foreach ($basket->getBasketElements() as $pos => $basketElement) {
            // create a new ErrorElement object
            $errorElement = new ErrorElement(
                $basket,
                $this->constraintValidatorFactory,
                $this->context,
                $this->context->getGroup()
            );

            $errorElement->with('basketElements['.$pos.']');

            // validate the basket element through the related service provider
            $this->productPool
                ->getProvider($basketElement->getProductCode())
                ->validateFormBasketElement($errorElement, $basketElement, $basket);
        }

        if (count($this->context->getViolations()) > 0) {
            $this->context->addViolationAt('basketElements', $constraint->message);
        }
    }
}
