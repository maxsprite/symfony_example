<?php

namespace App\Service\Api\V1;

use App\Entity\User;
use App\Utils\PasswordGenerator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserPhoneService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SmsService
     */
    private $smsService;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $criticalErrorUserMessage;

    /**
     * UserPhoneService constructor.
     * @param ContainerInterface $container
     * @param SmsService $smsService
     * @param SessionInterface $session
     */
    public function __construct(ContainerInterface $container, SmsService $smsService, SessionInterface $session)
    {
        $this->container  = $container;
        $this->smsService = $smsService;
        $this->session    = $session;

        $this->criticalErrorUserMessage = $this->container->getParameter('api_exception_user_message');
    }

    /**
     * @param string $phone
     * @return array
     */
    private function sendConfirmationPhoneCode(string $phone)
    {
        $confirmationCode = PasswordGenerator::generateConfirmationCode();
        $this->session->set('user_confirmation_phone_code', $confirmationCode);

        return $this->smsService->send($phone, 'Your confirmation code: ' . $confirmationCode);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function setPhoneAfterRegistration(Request $request, User $user): array
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');
            $phone         = $request->request->get('phone');

            $user->setPhone($phone);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->sendConfirmationPhoneCode($phone);

            return api_prepare_success_response($user);
        } catch (UniqueConstraintViolationException $exception) {
            return api_prepare_exception_response($exception, 'Phone is already used', $user);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->criticalErrorUserMessage, $user);
        }
    }

    /**
     * @param User $user
     * @return array
     */
    public function retrievePhoneCode(User $user): array
    {
        $this->sendConfirmationPhoneCode($user->getPhone());

        return api_prepare_success_response($user);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function confirmPhone(Request $request, User $user): array
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');

            if ($request->request->get('code') == $this->session->get('user_confirmation_phone_code')) {
                $user->setIsConfirmed(true);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            return api_prepare_success_response($user);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->criticalErrorUserMessage, $user);
        }
    }
}