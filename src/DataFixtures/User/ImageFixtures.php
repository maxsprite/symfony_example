<?php

namespace App\DataFixtures\User;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $users      = $manager->getRepository(User::class)->findAll();
        $filePath   = $this->container->getParameter('kernel.root_dir') . '/../public/fixtures/';
        $fileName   = 'image.jpg';
        $fileSystem = new Filesystem();

        /** @var User $user */
        foreach ($users as $user) {
            // Each user - copy fixture image because UploadedFile delete that
            $fileSystem->copy($filePath . $fileName, $filePath . 'uploaded_files/' . $fileName);

            $userImage = new User\Image();
            $file      = new UploadedFile($filePath . 'uploaded_files/' . $fileName, $fileName, null, null, true);

            $userImage->setUser($user);
            $userImage->setFile($file);

            $manager->persist($userImage);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
