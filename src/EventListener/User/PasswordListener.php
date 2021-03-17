<?php

namespace App\EventListener\User;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordListener
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function prePersist(UserInterface $user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }

    public function preUpdate(UserInterface $user, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($user);

        if (isset($changeSet['password'])) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
}