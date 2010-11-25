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


class BasketElementCollectionValidator extends ConstraintValidator
{

    /**
     * the product pool
     *
     * @var
     */
    protected $product_pool;

    /**
     * the basket
     * @var
     */
    protected $basket;


    /**
     * set the basket
     *
     * @param  \Sonata\Component\Basket\Basket $basket
     * @return void
     */
    public function setBasket(\Sonata\Component\Basket\Basket $basket)
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
     * @param  \Sonata\Component\Product\Pool $product_pool
     * @return void
     */
    public function setProductPool(\Sonata\Component\Product\Pool $product_pool)
    {
        $this->product_pool = $product_pool;
    }

    /**
     * return the product pool
     * 
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool()
    {
        return $this->product_pool;
    }

    /**
     * The validator asks each product repository to validate the related basket element
     *
     * @param  $basket_elements
     * @param Constraint $constraint
     * @return bool
     */
    public function isValid($basket_elements, Constraint $constraint)
    {
        $walker = $this->context->getGraphWalker();
        $group = $this->context->getGroup();
        $propertyPath = $this->context->getPropertyPath();

        foreach($basket_elements as $pos => $basket_element) {

            $errors = $this
                ->getProductPool()
                ->getRepository($basket_element->getProduct())
                ->validateFormBasketElement($basket_element);

            // global error, the all line is invalid
            if($errors['global']) {
                $this->context->setPropertyPath(sprintf('%s[%d]', $propertyPath, $pos));
                $this->context->setGroup($group);

                $this->context->addViolation(
                    $errors['global'][0],
                    $errors['global'][1],
                    $errors['global'][2]
                );
            }

            if(is_array($errors['fields']) && count($errors['fields']) > 0) {

                foreach($errors['fields'] as $name => $error) {
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