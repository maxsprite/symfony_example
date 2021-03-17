<?php

namespace App\Controller\Api\V1;

use App\Entity\Country;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class CountryController
 * @package App\Controller\Api\V1
 *
 * @Rest\Route("/api/v1/countries", name="api_v1_countries_")
 * @SWG\Tag(name="Countries")
 */
class CountryController extends BaseController
{
    /**
     * @Rest\Post("", name="index")
     * @SWG\Response(
     *     response="200",
     *     description="Return all available countries in database"
     * )
     */
    public function index()
    {
        $countries = $this->getDoctrine()->getRepository(Country::class)->findAll();
        $this->propertyView(api_prepare_success_response($countries));

        return $this->handlePropertyView(['read']);
    }
}
