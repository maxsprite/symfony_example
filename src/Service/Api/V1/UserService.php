<?php

namespace App\Service\Api\V1;

use App\Entity\Consultant;
use App\Entity\HashTag;
use App\Entity\User;
use App\Form\UserRegistrationType;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Custom outh for same route
     *
     * @param Request $request
     * @return array
     */
    public function auth(Request $request)
    {
        $entityManager   = $this->container->get('doctrine.orm.default_entity_manager');
        $passwordEncoder = $this->container->get('security.password_encoder');
        $jwtService      = $this->container->get('app_api_v1_jwt_service');

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $request->request->get('email'),
        ]);

        if ($user) {
            $isValid = $passwordEncoder->isPasswordValid($user, $request->request->get('password'));

            if ($isValid) {
                return $jwtService->createApiJwtResponseTokens($user);
            } else {
                return api_prepare_error_response('Bad credentials');
            }
        }

        return api_prepare_error_response('Bad credentials');
    }

    /**
     * Registration new user
     *
     * @param Request $request
     * @return array
     */
    public function registration(Request $request): array
    {
        $user = new User();

        try {
            $data          = $request->request->all();
            $entityManager = $this->container->get('doctrine.orm.entity_manager');
            $formFactory   = $this->container->get('form.factory');
            $form          = $formFactory->create(UserRegistrationType::class, $user);

            $form->submit($data);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var User $user */
                $user = $form->getData();
                $user->setRoles(['ROLE_USER']);

                $entityManager->persist($user);
                $entityManager->flush();

                return api_prepare_success_response($user);
            } else {
                return api_prepare_error_response((string) $form->getErrors(true, false), $user, 422);
            }
        } catch (UniqueConstraintViolationException $exception) {
            return api_prepare_exception_response($exception, 'Email or phone already exists', $user);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }

    /**
     * Update user/consultant profile fields
     *
     * @param Request $request
     * @param UserInterface|User $user
     * @return array
     */
    public function update(Request $request, UserInterface $user)
    {
        try {
            $data                     = json_decode($request->getContent(), true);
            $entityManager            = $this->container->get('doctrine.orm.default_entity_manager');
            $editableUserFields       = [
                'firstName',
                'lastName',
                'email',
                'phone',
                'description',
                'password',
            ];
            $editableConsultantFields = [
                'speciality',
                'price',
                'minTime',
            ];

            /** @var Consultant $consultant */
            $consultant = $user->getConsultant();

            // Create Consultant in processing update user profile fields
            if (array_key_exists('consultant', $data) && is_object($consultant) === false) {
                $consultant = new Consultant();
                $consultant->setUser($user);
            }

            foreach ($data as $userField => $userValue) {
//                $entityField = Inflector::camelize($userField);

                // Update editable User entity fields
                if (property_exists($user, $userField) && in_array($userField, $editableUserFields)) {
                    call_user_func([$user, 'set' . $userField], $userValue);
                }

                // Update editable Consultant entity fields
                if ($userField == 'consultant' && is_object($consultant)) {
                    foreach ($userValue as $consultantField => $consultantValue) {
                        if (property_exists($consultant, $consultantField) && in_array($consultantField, $editableConsultantFields)) {
                            call_user_func([$consultant, 'set' . $consultantField], $consultantValue);
                        }

                        // Custom logic for consultant hash tags
                        if ($consultantField == 'hashTags') {
                            // Before update hash tags we need clear previous tags
                            $consultant->getHashTags()->clear();

                            $consultant = $entityManager->merge($consultant);
                            $entityManager->flush();

                            foreach ($consultantValue as $hashItem) {
                                /** @var HashTag $hashTag */
                                $hashTag = HashTag::init(['name' => $hashItem]);
                                $consultant->addHashTag($hashTag);
                            }

                            $entityManager->persist($consultant);
                            $entityManager->flush();
                        }
                    }
                }
            }

            $entityManager->persist($user);

            if (is_object($consultant)) {
                $entityManager->persist($consultant);
            }

            $entityManager->flush();

            return api_prepare_success_response($user);
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }
}