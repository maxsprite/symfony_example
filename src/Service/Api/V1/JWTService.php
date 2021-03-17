<?php

namespace App\Service\Api\V1;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class JWTService
{
    /** @var ContainerInterface */
    private $container;

    /** @var JWTTokenManagerInterface */
    private $tokenManager;

    /** @var RefreshTokenManagerInterface */
    private $refreshTokenManager;

    /** @var ValidatorInterface */
    private $validator;

    /** @var int */
    private $ttl;

    /**
     * JWTService constructor.
     * @param ContainerInterface $container
     * @param JWTTokenManagerInterface $tokenManager
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ContainerInterface $container,
        JWTTokenManagerInterface $tokenManager,
        ValidatorInterface $validator
    ) {
        $this->container    = $container;
        $this->tokenManager = $tokenManager;
        $this->validator    = $validator;
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function createJwtTokens(UserInterface $user)
    {
        try {
            $refreshTokenManager = $this->container->get('gesdinet.jwtrefreshtoken.refresh_token_manager');
            $token               = $this->tokenManager->create($user);
            $refreshToken        = $refreshTokenManager->create();
            $datetime            = new \DateTime();

            $datetime->modify('+' . $this->container->getParameter('jwt_token_ttl') . ' seconds');

            $refreshToken->setUsername($user->getUsername());
            $refreshToken->setRefreshToken();
            $refreshToken->setValid($datetime);

            // Validate, that the new token is a unique refresh token
            $valid = false;
            while (false === $valid) {
                $valid = true;
                $errors = $this->validator->validate($refreshToken);
                if ($errors->count() > 0) {
                    foreach ($errors as $error) {
                        if ('refreshToken' === $error->getPropertyPath()) {
                            $valid = false;
                            $refreshToken->setRefreshToken();
                        }
                    }
                }
            }

            $refreshTokenManager->save($refreshToken);

            return [
                'token'         => $token,
                'refresh_token' => $refreshToken->getRefreshToken(),
            ];
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function prepareApiResponse($data)
    {
        if ($data instanceof \Exception) {
            return api_prepare_exception_response($data, 'Failed create JWT token');
        } else {
            return api_prepare_success_response($data);
        }
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function createApiJwtResponseTokens(UserInterface $user): array
    {
        return $this->prepareApiResponse($this->createJwtTokens($user));
    }
}