<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $country = Country::init([
            'name' => 'USA',
            'code' => 'usa',
        ]);
        $manager->persist($country);

        $country = Country::init([
            'name' => 'Ukraine',
            'code' => 'ukraine',
        ]);
        $manager->persist($country);

        $country = Country::init([
            'name' => 'Russia',
            'code' => 'russia',
        ]);
        $manager->persist($country);

        $manager->flush();
    }
}
