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

namespace Sonata\Component\Form;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;

class BasketValidator extends ConstraintValidator
{
    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var ContainerConstraintValidatorFactory
     */
    protected $constraintValidatorFactory;

    /**
     * @param ProductPool                         $productPool
     * @param ContainerConstraintValidatorFactory $constraintValidatorFactory
     */
    public function __construct(ProductPool $productPool, ContainerConstraintValidatorFactory $constraintValidatorFactory)
    {
        $this->productPool = $productPool;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
    }

    /**
     * The validator asks each product repository to validate the related basket element.
     *
     * @param BasketInterface $basket
     * @param Constraint      $constraint
     */
    public function validate($basket, Constraint $constraint): void
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
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('\'basketElements\'')
                ->addViolation();
        }
    }
}
