<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un utilisateur administrateur',
)]
class CreateAdminCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $name = $io->ask('Nom de l’administrateur');

        $email = $io->ask('Adresse e-mail');

        $plainPassword = $io->askHidden('Mot de passe');

        $user = new User();

        $user->setName($name);
        $user->setEmail($email);
        $user->setDeliveryAddress(null);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Administrateur créé avec succès.');

        return Command::SUCCESS;
    }
}
