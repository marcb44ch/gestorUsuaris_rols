<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Creem l'usuari
        $user = new User();
        $user->setEmail('test@t.com');
        
        // Xifrem la contrasenya "1234"
        $password = $this->hasher->hashPassword($user, '12345');
        $user->setPassword($password);

        // Opcional: Si tens rols, pots afegir-los
        // $user->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $manager->flush();
    }
}
