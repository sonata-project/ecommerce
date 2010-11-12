<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\DataFixtures\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Application\ProductBundle\Entity\Category;
use Application\ProductBundle\Entity\Product;

use Application\ProductBundle\Entity\ProductCategory;

class AllData implements FixtureInterface
{
    public function load($manager)
    {
        
        $id = 1;
        foreach(range(0, 15) as $level_1_id) {

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

            foreach(range(0, 25) as $product) {
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

            }

            foreach(range(0, 10) as $level_2_id) {
                $cat2 = new Category();
                $cat2->setName($category->getName().' : '.$level_2_id);
                $cat2->setSlug($category->getSlug().'-'.$level_2_id);
                $cat2->setDescription($category->getDescription());
                $cat2->setEnabled(true);
                $cat2->setCreatedAt(new \DateTime());
                $cat2->setUpdatedAt(new \DateTime());

                $manager->persist($cat2);

                $categories[] = $cat2;

                foreach(range(0, 5) as $level_3_id) {
                    $id++;
                    $cat3 = new Category();
                    $cat3->setName($cat2->getName().' : '.$level_3_id);
                    $cat3->setSlug($cat2->getSlug().'-'.$level_3_id);
                    $cat3->setDescription($category->getDescription());
                    $cat3->setEnabled(true);
                    $cat3->setCreatedAt(new \DateTime());
                    $cat3->setUpdatedAt(new \DateTime());

                    $manager->persist($cat3);

                    $cat2->addChildren($cat3);

                    $categories[] = $cat3;
                }


                $category->addChildren($cat2);

                $manager->flush();
            }


            $manager->flush();
        }


        $manager->flush();

        foreach($categories as $category) {
            foreach($products as $product) {
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
}