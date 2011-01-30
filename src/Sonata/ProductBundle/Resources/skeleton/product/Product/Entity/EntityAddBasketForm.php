<?php

/*
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\ProductBundle\Product\{{ product }};


use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\ChoiceField;
use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\HiddenField;

/**
 * This form is used to display the add to basket form
 *
 */
class {{ product }}AddBasketForm extends Form
{
    public function configure()
    {
        $this->add(new HiddenField('productId'));
        $this->add(new TextField('quantity'));
    }
}