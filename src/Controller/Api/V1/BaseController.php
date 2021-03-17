<?php

namespace App\Controller\Api\V1;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;

class BaseController extends AbstractFOSRestController
{
    /**
     * @var View
     */
    protected $view;

    protected function propertyView($data = null, $statusCode = null, array $headers = [])
    {
        return $this->view = parent::view($data, $statusCode, $headers);
    }

    /**
     * @param array $groups
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePropertyView(array $groups = [])
    {
        if (count($groups) > 0) {
            $this->view->getContext()->setGroups($groups);
        }

        return parent::handleView($this->view);
    }
}