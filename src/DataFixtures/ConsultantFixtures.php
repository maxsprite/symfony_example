<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Consultant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ConsultantFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var \Faker\Generator  */
    private $faker;

    /** @var array */
    private $companies;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->companies = $manager->getRepository(Company::class)->findAll();
        $users           = $manager->getRepository(User::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if ((bool) random_int(0, 1)) {
                $consultant = Consultant::init([
                    'user'       => $user,
                    'speciality' => $this->faker->sentence(2, true),
                    'company'    => $this->getRandomCompanyOrNull(),
                    'price'      => $this->faker->randomNumber(2),
                ]);

                $user->addRole('ROLE_CONSULTANT');

                $manager->persist($user);
                $manager->persist($consultant);
            }
        }

        $manager->flush();
    }

    private function getRandomCompanyOrNull()
    {
        $companiesCount = count($this->companies);
        $randKey        = random_int(0, $companiesCount - 1);

        if ((bool) random_int(0, 1)) {
            return $this->companies[$randKey];
        }

        return null;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CompanyFixtures::class,
        ];
    }
}
