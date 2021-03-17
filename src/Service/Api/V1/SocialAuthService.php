<?php

namespace App\Service\Api\V1;

use App\Entity\Country;
use App\Entity\User;
use App\Utils\PasswordGenerator;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

final class SocialAuthService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var JWTService
     */
    private $jwtService;

    /**
     * SocialAuthService constructor.
     *
     * @param ContainerInterface $container
     * @param JWTService $jwtService
     */
    public function __construct(ContainerInterface $container, JWTService $jwtService)
    {
        $this->container  = $container;
        $this->jwtService = $jwtService;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function facebookAuth(Request $request): array
    {
        $fb = new \Facebook\Facebook([
            'app_id'                => $this->container->getParameter('facebook_app_id'),
            'app_secret'            => $this->container->getParameter('facebook_app_secret'),
            'default_graph_version' => 'v5.0',
        ]);

        try {
            // Returns a `Facebook\FacebookResponse` object
            $entityManager = $this->container->get('doctrine.orm.entity_manager');
            $response      = $fb->get('/me?fields=id,name,email', $request->request->get('access_token'));
            $graphUser     = $response->getGraphUser();
            $user          = $entityManager->getRepository(User::class)->findOneByEmail($graphUser->getEmail());

            if ($user === null) {
                $user = User::init([
                    'country'   => $entityManager->getRepository(Country::class)->find(1),
                    'email'     => $graphUser->getEmail(),
                    'firstName' => (string) $graphUser->getFirstName(),
                    'lastName'  => (string) $graphUser->getLastName(),
                    'password'  => PasswordGenerator::generate(),
                ]);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->jwtService->createApiJwtResponseTokens($user);
        } catch(\Facebook\Exceptions\FacebookResponseException $exception) {
            return api_prepare_exception_response($exception, 'Graph returned an error: ' . $exception->getMessage());
        } catch(\Facebook\Exceptions\FacebookSDKException $exception) {
            return api_prepare_exception_response($exception, 'Facebook SDK returned an error: ' . $exception->getMessage());
        } catch (ORMException $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_orm_exception_user_message'));
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }

    private function googleUserInfoCurlRequest(string $accessToken) {
        header('Content-Type: application/json'); // Specify the type of data

        $ch            = curl_init('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $accessToken); // Initialise cURL
        $authorization = "Authorization: Bearer " . $accessToken; // Prepare the authorisation token

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization)); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects

        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection

        return json_decode($result, true);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function googleAuth(Request $request): array
    {
        try {
            $googleOauthUrl = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $request->request->get('access_token');
            $googleUser     = $this->googleUserInfoCurlRequest($request->request->get('access_token'));
            $entityManager  = $this->container->get('doctrine.orm.entity_manager');
            $user           = $entityManager->getRepository(User::class)->findOneByEmail($googleUser['email']);

            if ($user === null) {
                $user = User::init([
                    'country'   => $entityManager->getRepository(Country::class)->find(1),
                    'email'     => $googleUser['email'],
                    'firstName' => (string) $googleUser['given_name'],
                    'lastName'  => (string) $googleUser['family_name'],
                    'password'  => PasswordGenerator::generate(),
                ]);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->jwtService->createApiJwtResponseTokens($user);
        } catch (ORMException $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_orm_exception_user_message'));
        } catch (\Exception $exception) {
            return api_prepare_exception_response($exception, $this->container->getParameter('api_exception_user_message'));
        }
    }
}