<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\DataFixtures\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Application\Sonata\ProductBundle\Entity\Category;
use Application\Sonata\ProductBundle\Entity\Bottle as Product;

use Application\Sonata\CustomerBundle\Entity\Address;
use Application\Sonata\ProductBundle\Entity\Delivery;
use Application\Sonata\CustomerBundle\Entity\Customer;
use Application\Sonata\ProductBundle\Entity\ProductCategory;

class AllData implements FixtureInterface
{
    public function load($manager)
    {
        /*
        $ratio = 1;

        foreach (range(0, 200 * $ratio) as $id) {
            $customer = new Customer;
            $customer->setFirstname('user'.$id);
            $customer->setLastname('lastname'.$id);
            
            foreach (range(0, 2 * $ratio) as $di) {
                $address = new Address;
                $address->setName($customer->getFullname().' - billing '.$di);
                $address->setCustomer($customer);
                $address->setCurrent($di == 1);
                $address->setType(Address::TYPE_BILLING);
                $address->setFirstname(sprintf('John %d %d', $id, $di));
                $address->setLastname(sprintf('Doe'));
                $address->setAddress1(sprintf('%d sonata street', $id));
                $address->setPostcode(12342);
                $address->setCity('Symfony City');
                $address->setCountryCode('FR');
                $address->setPhone('42');
                $address->setCreatedAt(new \DateTime());
                $address->setUpdatedAt(new \DateTime());

                $manager->persist($address);
            }

            foreach (range(0, 2 * $ratio) as $di) {
                $address = new Address;
                $address->setCustomer($customer);
                $address->setName($customer->getFullname().' - delivery '.$di);
                $address->setCurrent($di == 1);
                $address->setType(Address::TYPE_DELIVERY);
                $address->setFirstname(sprintf('John %d %d', $id, $di));
                $address->setLastname(sprintf('Doe'));
                $address->setAddress1(sprintf('%d sonata street', $id));
                $address->setPostcode(12342);
                $address->setCity('Symfony City');
                $address->setCountryCode('FR');
                $address->setPhone('42');
                $address->setCreatedAt(new \DateTime());
                $address->setUpdatedAt(new \DateTime());

                $manager->persist($address);
            }


            $manager->persist($customer);

            if ($id % 100 == 0) {
                $manager->flush();
            }
        }

        $manager->flush();

        $id = 1;
        foreach (range(0, 5 * $ratio) as $level_1_id) {

            $categories = array();
            $products = array();

            $id++;
            
            $category = new Category();
            $category->setName('Category '.$level_1_id);
            $category->setSlug('category-'.$level_1_id);
            $category->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $category->setEnabled(true);
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());

            $categories[] = $category;
            
            $manager->persist($category);

            foreach (range(0, 10 * $ratio) as $product) {
                
                $id++;
                $product = new Product();
                $product->setName('product '.$id);
                $product->setSku(sprintf('%06d', $id));
                $product->setSlug('product-'.$product->getSku());
                $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
                $product->setPrice(3.33);
                $product->setVat(19.60);
                $product->setStock(100);
                $product->setEnabled(true);
                $product->setCreatedAt(new \DateTime());
                $product->setUpdatedAt(new \DateTime());

                $products[] = $product;
                
                $manager->persist($product);

                // add product delivery

                $delivery = new Delivery;
                $delivery->setCountryCode('FR');
                $delivery->setCode('free');
                $delivery->setEnabled(true);
                $delivery->setPerItem(2);
                $delivery->setProduct($product);
                $delivery->setCreatedAt(new \DateTime());
                $delivery->setUpdatedAt(new \DateTime());

                $manager->persist($delivery);
                
                $delivery = new Delivery;
                $delivery->setCountryCode('GB');
                $delivery->setCode('free');
                $delivery->setEnabled(true);
                $delivery->setPerItem(2);
                $delivery->setProduct($product);
                $delivery->setCreatedAt(new \DateTime());
                $delivery->setUpdatedAt(new \DateTime());

                $manager->persist($delivery);

                $manager->flush();
            }

            $manager->flush();

            foreach (range(0, 2 * $ratio) as $level_2_id) {
                $cat2 = new Category();
                $cat2->setName($category->getName().' : '.$level_2_id);
                $cat2->setSlug($category->getSlug().'-'.$level_2_id);
                $cat2->setDescription($category->getDescription());
                $cat2->setEnabled(true);
                $cat2->setCreatedAt(new \DateTime());
                $cat2->setUpdatedAt(new \DateTime());

                $categories[] = $cat2;

                foreach (range(0, 2 * $ratio) as $level_3_id) {
                    $id++;
                    $cat3 = new Category();
                    $cat3->setName($cat2->getName().' : '.$level_3_id);
                    $cat3->setSlug($cat2->getSlug().'-'.$level_3_id);
                    $cat3->setDescription($category->getDescription());
                    $cat3->setEnabled(true);
                    $cat3->setCreatedAt(new \DateTime());
                    $cat3->setUpdatedAt(new \DateTime());

                    $cat3->setParent($cat2);
                    
                    $manager->persist($cat3);

                    $categories[] = $cat3;
                }

                $category->addChildren($cat2);

                $manager->persist($cat2);


            }
            $manager->flush();

            foreach ($categories as $category) {
                foreach ($products as $product) {
                    $product_category = new ProductCategory;
                    $product_category->setProduct($product);
                    $product_category->setCategory($category);

                    $product_category->setEnabled(true);
                    $product_category->setCreatedAt(new \DateTime());
                    $product_category->setUpdatedAt(new \DateTime());

                    $manager->persist($product_category);
                }
            }

            $manager->flush();
        }

        $manager->flush();
        */
    }
}