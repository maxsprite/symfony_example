<?php

namespace App\Service\Api\V1\User;

use App\Entity\Interfaces\MediaInterface;
use App\Entity\User;
use App\Form\User\UploadImageType;
use Doctrine\ORM\EntityManagerInterface;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class MediaService
{
    /** @var ContainerInterface  */
    private $container;

    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container     = $container;
        $this->entityManager = $entityManager;
    }

    private function generateImageUrl(Request $request, MediaInterface $media)
    {
        return $request->getUriForPath($this->container->getParameter('app.path.user_image_gallery')) . '/' . $media->getFileName();
    }

    private function generateVideoUrl(Request $request, MediaInterface $media)
    {
        return $request->getUriForPath($this->container->getParameter('app.path.user_video_gallery')) . '/' . $media->getFileName();
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return array
     */
    public function getUserImageList(Request $request, UserInterface $user): array
    {
        $userImages     = $user->getImages();
        $responseImages = [];

        /** @var User\Image $userImage */
        foreach ($userImages as $userImage) {
            $responseImages[] = [
                'id'        => $userImage->getId(),
                'url'       => $this->generateImageUrl($request, $userImage),
                'createdAt' => $userImage->getCreatedAt(),
                'updatedAt' => $userImage->getUpdatedAt(),
            ];
        }

        return api_prepare_success_response($responseImages);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return array
     */
    public function getUserVideoList(Request $request, UserInterface $user): array
    {
        $userVideos     = $user->getVideos();
        $responseVideos = [];

        /** @var User\Video $userImage */
        foreach ($userVideos as $userVideo) {
            $responseVideos[] = [
                'id'        => $userVideo->getId(),
                'url'       => $this->generateVideoUrl($request, $userVideo),
                'createdAt' => $userVideo->getCreatedAt(),
                'updatedAt' => $userVideo->getUpdatedAt(),
            ];
        }

        return api_prepare_success_response($responseVideos);
    }

    /**
     * @param Request $request
     * @param UserInterface|User $user
     *
     * @return array
     */
    public function uploadAvatar(Request $request, UserInterface $user): array
    {
        try {
            $user->setAvatarFile($request->files->get('file'));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return api_prepare_success_response($user);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }

    public function uploadImage(Request $request, UserInterface $user): array
    {
        $userImage = new User\Image();

        try {
            $userImage
                ->setUser($user)
                ->setFile($request->files->get('file'))
            ;

            $this->entityManager->persist($userImage);
            $this->entityManager->flush();

            return api_prepare_success_response($userImage);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $exception->getMessage());
        }
    }

    private function getUploadPathWithParameter(string $parameter): string
    {
        return $this->container->getParameter('kernel.project_dir') . '/public' . $parameter . '/';
    }

    private function createVideoThumbnail(User\Video $userVideo)
    {
        $projectDir = $this->container->getParameter('kernel.project_dir');
        $videoPath  = $projectDir
            . '/public'
            . $this->container->getParameter('app.path.user_video_gallery')
            . '/'
            . $userVideo->getFileName();

        // FFMpeg for create video thumbnail and get duration time
        $ffmpeg = FFMpeg::create();
        $video  = $ffmpeg->open($videoPath);

        $thumbnailFileName = 'video_' . $userVideo->getId() . '_thumbnail.jpg';

        // Save thumbnail image of video file
        $video
            ->frame(TimeCode::fromSeconds(1))
            ->save('/tmp/' . $thumbnailFileName);

        // VichUploader support only UploadedFile in test mode for manual insert file to entity
        $uploadedFile = new UploadedFile('/tmp/' . $thumbnailFileName, $thumbnailFileName, null, null, true);

        $userVideo->setThumbnailFile($uploadedFile);

        // Duration value in seconds
        $userVideo->setDuration($video->getStreams()->videos()->first()->get('duration'));

        $this->entityManager->persist($userVideo);
        $this->entityManager->flush();

        // Delete tmp uploaded thumbnail file
//        unlink('/tmp/' . $thumbnailFileName);

        return true;
    }

    public function uploadVideo(Request $request, UserInterface $user): array
    {
        $userVideo = new User\Video();

        try {
            $userVideo
                ->setUser($user)
                ->setFile($request->files->get('file'))
            ;

            $this->entityManager->persist($userVideo);
            $this->entityManager->flush();

            $this->createVideoThumbnail($userVideo);

            return api_prepare_success_response($userVideo);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $exception->getMessage());
        }
    }
}