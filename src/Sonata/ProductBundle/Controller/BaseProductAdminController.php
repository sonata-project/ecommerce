<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller;

use Sonata\BaseApplicationBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class BaseProductAdminController extends Controller
{

    /**
     * return a Response object, the parameters array will get an extra parameter (product)
     *
     * @param  $view
     * @param array $parameters
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @return Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if($this->admin->isChild()) {

            $admin = $this->admin->getParent();
            $id = $this->container->get('request')->get($admin->getIdParameter());
            
            $product = $admin->getObject($id);

            $parameters['product'] = $product;
        } else if(isset($parameters['object'])) {

            $parameters['product'] = $parameters['object'];
        }

        return parent::render($view, $parameters, $response);
    }
}