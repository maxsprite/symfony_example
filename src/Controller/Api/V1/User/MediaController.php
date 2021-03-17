<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\V1\BaseController;
use App\Service\Api\V1\User\MediaService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User\Image;
use App\Entity\User\Video;

/**
 * @Rest\Route("/api/v1/user/media", name="api_v1_user_media_")
 * @SWG\Tag(name="User media")
 */
class MediaController extends BaseController
{
    /**
     * @Rest\Post("/upload/avatar", name="upload_avatar")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Upload new avatar"
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     type="file",
     *     in="formData"
     * )
     */
    public function uploadAvatar(Request $request, MediaService $mediaService)
    {
        $this->propertyView($mediaService->uploadAvatar($request, $this->getUser()));

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/images", name="images")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Get images file urls",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Image::class, groups={"read"}))
     *     )
     * )
     */
    public function images(Request $request, MediaService $mediaService)
    {
        $this->propertyView($mediaService->getUserImageList($request, $this->getUser()));

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/upload/image", name="upload_image")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Upload new image"
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     type="file",
     *     in="formData"
     * )
     */
    public function uploadImages(Request $request, MediaService $mediaService)
    {
        $this->propertyView($mediaService->uploadImage($request, $this->getUser()));

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/videos", name="videos")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Get videos file urls",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Video::class, groups={"read"}))
     *     )
     * )
     */
    public function videos(Request $request, MediaService $mediaService)
    {
        $this->propertyView($mediaService->getUserVideoList($request, $this->getUser()));

        return $this->handlePropertyView(['read']);
    }

    /**
     * @Rest\Post("/upload/video", name="upload_video")
     *
     * @SWG\Response(
     *     response="200",
     *     description="Upload new video"
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     type="file",
     *     in="formData"
     * )
     */
    public function uploadVideo(Request $request, MediaService $mediaService)
    {
        $this->propertyView($mediaService->uploadVideo($request, $this->getUser()));

        return $this->handlePropertyView(['read']);
    }
}
