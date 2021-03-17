<?php

namespace App\DataFixtures;

use App\Entity\Consultant;
use App\Entity\RequestCall;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class RequestCallFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $consultants = $manager->getRepository(Consultant::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($consultants as $consultant) {
            $requestCall = RequestCall::init([
                'client' => $this->getRandNonCurrentUser($users, $consultant->getUser()),
                'consultant' => $consultant->getUser(),
                'amount' => $this->faker->randomNumber(2),
                'time' => $this->faker->randomNumber(2),
                'taxClientReject' => 30,
                'taxConsultantSuccess' => 15,
            ]);

            $manager->persist($requestCall);
        }

        $manager->flush();
    }

    private function getRandNonCurrentUser($users, $currentUser)
    {
        $count = count($users) - 1;
        $randKey = random_int(0, $count);

        if ($users[$randKey]->getId() == $currentUser->getId()) {
            $this->getRandNonCurrentUser($users, $currentUser);
        }

        return $users[$randKey];
    }

    public function getDependencies()
    {
        return [
            ConsultantFixtures::class
        ];
    }
}
