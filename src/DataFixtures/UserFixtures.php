<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const DEFAULT_NAME = 'admin';
    public const DEFAULT_PASSWORD = 'admin';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setUsername(self::DEFAULT_NAME)
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, self::DEFAULT_PASSWORD));

        $manager->persist($user);
        $manager->flush();
    }
}

