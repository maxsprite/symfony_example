<?php

namespace App\Controller\Api\V1;

use App\Service\Api\V1\RecoveryPasswordService;
use App\Service\Api\V1\UserPhoneService;
use App\Service\Api\V1\UserService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Rest\Route("/api/v1/user", name="api_v1_user_")
 *
 * @SWG\Tag(name="User")
 */
class UserController extends BaseController
{
    /**
     * @Rest\Post("/recovery/password/phone", name="recovery_password_with_phone")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Recovery password by phone number"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function recoveryPasswordWithPhone(Request $request, RecoveryPasswordService $recoveryPasswordService)
    {
        $result = $recoveryPasswordService->recoveryPasswordWithPhone($request);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/registration", name="create")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Register new user"
     * )
     *
     * @SWG\Parameter(
     *     name="country",
     *     type="number",
     *     in="formData",
     *     description="Id of country"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     type="string",
     *     in="formData"
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     type="string",
     *     in="formData"
     * )
     */
    public function registration(Request $request, UserService $userService)
    {
        $result = $userService->registration($request);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Route("/set/phone", name="set_phone", methods={"POST"})
     *
     * @SWG\Response(
     *     response="200",
     *     description="Set phone number after registration"
     * )
     *
     * @SWG\Parameter(
     *     name="phone",
     *     type="string",
     *     in="formData"
     * )
     */
    public function setPhoneAfterRegistration(Request $request, UserPhoneService $userPhoneService)
    {
        $result = $userPhoneService->setPhoneAfterRegistration($request, $this->getUser());
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/confirm/phone", name="comfirm_phone")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Confirm phone number with SMS code"
     * )
     *
     * @SWG\Parameter(
     *     name="code",
     *     type="string",
     *     in="formData"
     * )
     */
    public function confirmPhone(Request $request, UserPhoneService $userPhoneService, EntityManagerInterface $entityManager)
    {
        $result = $userPhoneService->confirmPhone($request, $this->getUser());
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/confirm/phone/retrieve", name="comfirm_phone_retrieve")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Retrieve confirmation SMS code"
     * )
     */
    public function retrievePhoneCode(UserPhoneService $userPhoneService)
    {
        $result = $userPhoneService->retrievePhoneCode($this->getUser());
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }
}
