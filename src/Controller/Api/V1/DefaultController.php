<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Service\Api\V1\RecoveryPasswordService;
use App\Service\Api\V1\SocialAuthService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller\Api\V1
 *
 * @Rest\Route("/api/v1", name="api_v1_")
 */
class DefaultController extends BaseController
{
}