<?php

namespace App\DataFixtures;


use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $member = new Member();

        $member->setPassword($this->passwordHasher->hashPassword(
            $member,
            'member'
        ));
        $member->setUsername('member');
        $manager->persist($member);

        $admin = new Member();

        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'admin'
        ));
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}