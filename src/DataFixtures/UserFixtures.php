<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    // ALTER TABLE `user` AUTO_INCREMENT=1;

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email'     => 'alexandre.peneau@gmail.com',
                'password'  => 'a_p_efrei_b3_in',
                'roles'     => ['ROLE_ADMIN']
            ],
            [
                'email'     => 'isabelle.test@gmail.com',
                'password'  => 'i_t_efrei_b3_in',
                'roles'     => ['ROLE_MANAGER']
            ],
            [
                'email'     => 'john.test@gmail.com',
                'password'  => 'j_t_efrei_b3_in',
                'roles'     => ['ROLE_USER']
            ],
            [
                'email'     => 'matthieu.test@gmail.com',
                'password'  => 'm_t_efrei_b3_in',
                'roles'     => ['ROLE_USER']
            ],
            [
                'email'     => 'lucas.test@gmail.com',
                'password'  => 'l_t_efrei_b3_in',
                'roles'     => ['ROLE_USER']
            ]
        ];

        foreach($users as $userinfos) {
                $user = new User();
                $user->setEmail($userinfos['email']);

                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    $userinfos['password']
                );
                $user->setPassword($hashedPassword);
                $user->setRoles($userinfos['roles']);
                $manager->persist($user);
        }

        $manager->flush();
    }
}
