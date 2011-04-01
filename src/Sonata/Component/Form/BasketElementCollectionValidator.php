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

class BasketElementCollectionValidator extends ConstraintValidator
{

    /**
     * the product pool
     *
     * @var
     */
    protected $productPool;

    /**
     * the basket
     * @var
     */
    protected $basket;

    /**
     * set the basket
     *
     * @param BasketInterface $basket
     * @return void
     */
    public function setBasket(BasketInterface $basket)
    {
        $this->basket = $basket;
    }

    /**
     * return the basket
     *
     * @return \Sonata\Component\Basket\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * set the product pool
     *
     * @param  \Sonata\Component\Product\Pool $productPool
     * @return void
     */
    public function setProductPool(ProductPool $productPool)
    {
        $this->productPool = $productPool;
    }

    /**
     * return the product pool
     * 
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * The validator asks each product repository to validate the related basket element
     *
     * @param  $basketElements
     * @param Constraint $constraint
     * @return bool
     */
    public function isValid($basketElements, Constraint $constraint)
    {
        $walker = $this->context->getGraphWalker();
        $group = $this->context->getGroup();
        $propertyPath = $this->context->getPropertyPath();

        foreach ($basketElements as $pos => $basketElement) {

            $errors = $this
                ->getProductPool()
                ->getRepository($basketElement->getProduct())
                ->validateFormBasketElement($basketElement);

            // global error, the all line is invalid
            if ($errors['global']) {
                $this->context->setPropertyPath(sprintf('%s[%d]', $propertyPath, $pos));
                $this->context->setGroup($group);

                $this->context->addViolation(
                    $errors['global'][0],
                    $errors['global'][1],
                    $errors['global'][2]
                );
            }

            if (is_array($errors['fields']) && count($errors['fields']) > 0) {

                foreach ($errors['fields'] as $name => $error) {
                    $this->context->setPropertyPath(sprintf('%s[%d][%s]', $propertyPath, $pos, $name));
                    $this->context->setGroup($group);

                    $this->context->addViolation(
                        $error[0],
                        $error[1],
                        $error[2]
                    );
                }
            }
        }

        return count($this->context->getViolations()) == 0;
    }
}