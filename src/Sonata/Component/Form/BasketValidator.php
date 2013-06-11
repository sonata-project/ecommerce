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

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory;
use Sonata\AdminBundle\Validator\ErrorElement;

class BasketValidator extends ConstraintValidator
{
    protected $productPool;

    protected $basket;

    protected $constraintValidatorFactory;

    public function __construct(ProductPool $productPool, ConstraintValidatorFactory $constraintValidatorFactory)
    {
        $this->productPool  = $productPool;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
    }

    /**
     * The validator asks each product repository to validate the related basket element
     *
     * @param BasketInterface   $basket
     * @param Constraint        $constraint
     */
    public function validate($basket, Constraint $constraint)
    {
        /*
         * @todo : check 2.3 compatibility
         */
        $group = $this->context->getGroup();
        $contextPropertyPath = $this->context->getPropertyPath();

        foreach ($basket->getBasketElements() as $pos => $basketElement) {
            // update the property path value
            $propertyPath = sprintf('%s[%d]', $contextPropertyPath, $pos);

            // create a new ErrorElement object
            $errorElement = new ErrorElement(
                $basketElement,
                $this->constraintValidatorFactory,
                $this->context,
                $group
            );

            // validate the basket element through the related service provider
            $this->productPool
                ->getProvider($basketElement->getProductCode())
                ->validateFormBasketElement($errorElement, $basketElement, $basket);
        }

        if (isset($propertyPath)) {
            $contextPropertyPath = $propertyPath;
        }

//        $this->context->setGroup($group);

        if (count($this->context->getViolations()) > 0) {
            $context->addViolationAt($contextPropertyPath, $constraint->message, array(), null);
        }

        return;
    }
}