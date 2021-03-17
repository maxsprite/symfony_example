<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AdminUserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $adminUser = AdminUser::init([
             'email' => 'admin@hire.com',
             'roles' => ['ROLE_ADMIN'],
             'password' => 'admin1234',
         ]);

         $manager->persist($adminUser);

        $manager->flush();
    }
}
