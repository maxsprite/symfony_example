<?php

namespace App\Service\Api\V1;

use App\Entity\User;
use App\Utils\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class RecoveryPasswordService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SmsService
     */
    private $smsService;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, SmsService $smsService)
    {
        $this->container     = $container;
        $this->entityManager = $entityManager;
        $this->smsService    = $smsService;
    }

    public function recoveryPasswordWithPhone(Request $request)
    {
        try {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneByPhone($request->request->get('phone'));

            if ($user !== null) {
                $newPassword = PasswordGenerator::generate();

                $user->setPassword($newPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->smsService->send($user->getPhone(), 'Your new password: ' . $newPassword);

                return api_prepare_response(['success' => true]);
            }

            return api_prepare_response(['success' => false]);
        } catch (ORMException $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_orm_exception_user_message'));
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }
}