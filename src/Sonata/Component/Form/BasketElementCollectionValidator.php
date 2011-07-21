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

class BasketElementCollectionValidator extends ConstraintValidator
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
     * @param BasketElement $basketElements
     * @param Constraint $constraint
     * @return bool
     */
    public function isValid($basketElements, Constraint $constraint)
    {
        $group = $this->context->getGroup();
        $propertyPath = $this->context->getPropertyPath();

        foreach ($basketElements as $pos => $basketElement) {
            // update the property path value
            $this->context->setPropertyPath(sprintf('%s[%d]', $propertyPath, $pos));

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
                ->validateFormBasketElement($errorElement, $basketElement);
        }

        $this->context->setPropertyPath($propertyPath);
        $this->context->setGroup($group);

        if (count($this->context->getViolations()) == 0) {
            return true;
        }

        $this->setMessage($constraint->message, array());

        return false;
    }
}