<?php

namespace App\Service\Api\V1;

use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FollowService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $criticalErrorUserMessage;

    /**
     * @var string
     */
    private $criticalORMErrorUserMessage;

    /**
     * UserPhoneService constructor.
     * @param ContainerInterface $container
     * @param SessionInterface $session
     */
    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
        $this->container  = $container;
        $this->session    = $session;

        $this->criticalErrorUserMessage = $this->container->getParameter('api_exception_user_message');
        $this->criticalORMErrorUserMessage = $this->container->getParameter('api_orm_exception_user_message');
    }

    /**
     * @param $currentUser
     * @param $followerUser
     * @return array
     */
    public function setFollower($currentUser, $followerUser): array
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');

            $currentUser->addFollowing($followerUser);

            $entityManager->persist($currentUser);
            $entityManager->flush();

            return api_prepare_success_response($followerUser);
        } catch (ORMException $exception) {
            return api_prepare_exception_response($exception, $this->criticalORMErrorUserMessage,  $followerUser);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->criticalErrorUserMessage, $followerUser);
        }
    }

    /**
     * @param $currentUser
     * @param $followerUser
     * @return array
     */
    public function removeFollower($currentUser, $followerUser): array
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');

            $followerUser->removeFollower($currentUser);

            $entityManager->persist($followerUser);
            $entityManager->flush();

            return api_prepare_success_response($followerUser);
        } catch (ORMException $exception) {
            return api_prepare_exception_response($exception, $this->criticalORMErrorUserMessage,  $followerUser);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->criticalErrorUserMessage, $followerUser);
        }
    }
}