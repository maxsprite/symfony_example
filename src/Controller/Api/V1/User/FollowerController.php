<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\V1\BaseController;
use App\Entity\User;
use App\Service\Api\V1\FollowService;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Rest\Route("/api/v1/user/follower", name="api_v1_follower_")
 * @SWG\Tag(name="User follower")
 */
class FollowerController extends BaseController
{
    /**
     * @Rest\Post("/set/{id}", name="set_follower")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Set follower"
     * )
     */
    public function setFollower(User $followerUser, FollowService $followerService)
    {
        $result = $followerService->setFollower($this->getUser(), $followerUser);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Get("/list", name="list")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Get followers list",
     * )
     */
    public function followersList()
    {
        $this->propertyView(api_prepare_success_response($this->getUser()->getFollowings()));

        return $this->handlePropertyView();
    }

    /**
     * @Rest\Delete("/remove/{id}", name="remove_follower")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Romove follower"
     * )
     */
    public function removeFollower(User $followerUser, FollowService $followerService)
    {
        $result = $followerService->removeFollower($this->getUser(), $followerUser);
        $this->propertyView($result, $result['code']);

        return $this->handlePropertyView(['read']);
    }
}