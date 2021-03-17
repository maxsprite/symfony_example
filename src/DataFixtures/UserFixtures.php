<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Countable|Country
     */
    private $countries = [];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ObjectManager $manager, ContainerInterface $container)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker           = Factory::create();
        $this->container       = $container;
    }

    private function getUploadedAvatar(UserInterface $user)
    {
        $generatedAvatar = file_get_contents('https://eu.ui-avatars.com/api/?name='. $user->getFirstName() . '+' . $user->getLastName());
        $filePath        = $this->container->getParameter('kernel.root_dir') . '/../public/uploads/images/user/avatar/';
        $fileName        = uniqid() . '.png';

        file_put_contents($filePath . $fileName, $generatedAvatar);

        return new UploadedFile($filePath . $fileName, $fileName, null, null, true);
    }

    private function makeTestUser(ObjectManager $manager)
    {
        $password = 'test1234';

        /** @var User $user */
        $user = User::init([
            'balance'     => $this->faker->randomNumber(3),
            'country'     => $this->getRandCountry(),
            'email'       => 'test@hire.com',
            'firstName'   => 'John',
            'lastName'    => 'Doe',
            'phone'       => $this->faker->phoneNumber,
            'description' => $this->faker->text(),
            'password'    => $password,
            'roles'       => ['ROLE_USER'],
        ]);

        $user->setAvatarFile($this->getUploadedAvatar($user));

        dump('--------');
        dump($user->getEmail(), $password);
        dump('--------');

        $manager->persist($user);
    }

    public function load(ObjectManager $manager)
    {
        $this->countries = $manager->getRepository(Country::class)->findAll();

        $this->makeTestUser($manager);

        for($i = 0; $i < 10; $i++) {
            $password = $this->faker->password;

            /** @var User $user */
            $user = User::init([
                'balance'     => $this->faker->randomNumber(3),
                'country'     => $this->getRandCountry(),
                'email'       => $this->faker->email,
                'firstName'   => $this->faker->firstName,
                'lastName'    => $this->faker->lastName,
                'phone'       => $this->faker->phoneNumber,
                'description' => $this->faker->text(),
                'password'    => $password,
                'roles'       => ['ROLE_USER'],
            ]);

            $user->setAvatarFile($this->getUploadedAvatar($user));

            dump('--------');
            dump($user->getEmail(), $password);
            dump('--------');

            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getRandCountry()
    {
        $countriesCount = count($this->countries);
        $randKey        = random_int(0, $countriesCount - 1);

        return $this->countries[$randKey];
    }

    public function getDependencies()
    {
        return [
            CountryFixtures::class
        ];
    }
}
