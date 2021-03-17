<?php

namespace App\DataFixtures;

use App\Entity\Constants\RequestTransactionStatus;
use App\Entity\RequestCall;
use App\Entity\RequestTransaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RequestTransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $requestCalls = $manager->getRepository(RequestCall::class)->findAll();

        /** @var RequestCall $requestCall */
        foreach ($requestCalls as $requestCall) {
            $requestTransaction = RequestTransaction::init([
                'requestCall' => $requestCall,
                'status' => $this->getRandomStatus(),
            ]);
            $manager->persist($requestTransaction);
        }

        $manager->flush();
    }

    private function getRandomStatus()
    {
        $statuses = RequestTransactionStatus::getValues();
        $randKey = random_int(0, count($statuses) - 1);

        return $statuses[$randKey];
    }

    public function getDependencies()
    {
        return [
            RequestCallFixtures::class
        ];
    }
}
