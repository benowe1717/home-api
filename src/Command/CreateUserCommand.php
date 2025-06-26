<?php

/**
 * Symfony Command for Creating a User Entity
 *
 * PHP version 8.4
 *
 * @category  Command
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Symfony Command for Creating a User Entity
 *
 * PHP version 8.4
 *
 * @category  Command
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
#[AsCommand(
    name: 'app:create-user',
    description: 'Create a User',
    hidden: false
)]
class CreateUserCommand extends Command
{
    private mixed $error;
    private EntityManagerInterface $entityManagerInterface;
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * CreateUserCommand constructor
     *
     * @param EntityManagerInterface      $entityManagerInterface The Entity Manager
     * @param UserPasswordHasherInterface $userPasswordHasher The Password Hasher
     **/
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Generate a cryptographically secure random password excluding look-alike
     * characters with digits, uppercase, lowercase, and special characters
     * of 24 characters in length.
     *
     * @return string
     **/
    private function generatePassword(): string
    {
        $keyspace = '123456789';
        $keyspace .= 'abcdefghijkmnopqrstuvwxyz';
        $keyspace .= 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $keyspace .= '!@#$%^&*-_+=?';
        $max = mb_strlen($keyspace, '8bit') - 1;

        $password = '';
        for ($i = 0; $i < 24; $i++) {
            $password .= $keyspace[random_int(0, $max)];
        }
        return $password;
    }

    /**
     * Generate a cryptographically secure random API Key in alphanumeric format.
     *
     * @return string
     **/
    private function generateApiKey(): string
    {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    /**
     * Create a User Entity from the given email address.
     *
     * @param string $email The Email Address of the User
     *
     * @return array[App\Entity\User, string]
     **/
    private function createUser(string $email): array
    {
        $user = new User();
        $user->setEmail($email);

        $plainPassword = $this->generatePassword();
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        $user->setApikey($this->generateApiKey());

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
        } catch (Exception $e) {
            $this->error = "{$e->getCode()}::{$e->getMessage()}";
            return [];
        }

        return [$user, $plainPassword];
    }

    /**
     * Configure the command
     *
     * @return void
     **/
    protected function configure(): void
    {
        $this->setHelp('Create a User with a random password and API Key')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'The email address of the User'
            );
    }

    /**
     * Method to control what happens when running the command
     *
     * @param InputInterface  $input  The optional parameters passed to the command
     * @param OutputInterface $output The returned value of the command
     *
     * @return int
     **/
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = trim(strtolower($input->getArgument('email')));

        $userDetails = $this->createUser($email);
        if (0 === count($userDetails)) {
            $io->error(sprintf('ERROR: %s', $this->error));
            return Command::FAILURE;
        }

        $user = $userDetails[0];
        $password = $userDetails[1];

        if (false === $user) {
            $io->error(sprintf('ERROR: Unable to create User!'));
            return Command::FAILURE;
        }

        $io->success('User created successfully! Details below:');
        $output->writeln("Username: {$email}");
        $output->writeln("Password: {$password}");
        $output->writeln("API Key: {$user->getApiKey()}");
        return Command::SUCCESS;
    }
}
