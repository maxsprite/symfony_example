<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\V1\BaseController;
use App\Service\Api\V1\SocialAuthService;
use App\Service\Api\V1\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package App\Controller\Api\V1\User
 *
 * @Rest\Route("/api/v1/user/auth", name="api_v1_user_auth_")
 * @SWG\Tag(name="User auth")
 */
class AuthController extends BaseController
{
    /**
     * @Rest\Post("", name="index")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Get user auth JWT token by 'email' and 'password' fields"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string"
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function auth(Request $request, UserService $userService)
    {
        $result = $userService->auth($request);

        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/facebook", name="facebook")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Facebook auth with access_token"
     * )
     *
     * @param Request $request
     * @param SocialAuthService $socialAuthService
     *
     * @return Response
     * @throws \Exception
     */
    public function facebookAuth(Request $request, SocialAuthService $socialAuthService)
    {
        $result = $socialAuthService->facebookAuth($request);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/google", name="google")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Google auth with access_token"
     * )
     *
     * @param Request $request
     * @param SocialAuthService $socialAuthService
     *
     * @return Response
     * @throws \Exception
     */
    public function googleAuth(Request $request, SocialAuthService $socialAuthService)
    {
        $result = $socialAuthService->googleAuth($request);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }
}
