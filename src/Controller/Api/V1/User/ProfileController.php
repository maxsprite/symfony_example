<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\V1\BaseController;
use App\Service\Api\V1\SocialAuthService;
use App\Service\Api\V1\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package App\Controller\Api\V1\User
 *
 * @Rest\Route("/api/v1/user/profile", name="api_v1_user_profile_")
 * @SWG\Tag(name="User profile")
 */
class ProfileController extends BaseController
{
    /**
     * @Rest\Post("", name="index"))
     *
     * @SWG\Response(
     *     response="200",
     *     description="Retrieve user profile data fields",
     *     @Model(type=User::class, groups={"read"})
     * )
     */
    public function profile(SocialAuthService $socialAuthService)
    {
        $this->propertyView(api_prepare_success_response($this->getUser()));
        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/update", name="update")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Update user/consultant profile fields",
     *     @Model(type=User::class, groups={"read"})
     * )
     */
    public function update(Request $request, UserService $userService)
    {
        $this->propertyView($userService->update($request, $this->getUser()));
        return $this->handlePropertyView(['read']);
    }
}
