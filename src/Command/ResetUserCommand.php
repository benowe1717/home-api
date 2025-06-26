<?php

/**
 * Symfony Command for Resetting a User Entity's Password or API Key
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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Symfony Command for Resetting a User Entity's Password or API Key
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
    name: 'app:reset-user',
    description: 'Reset a Password or API Key for a User',
)]
class ResetUserCommand extends Command
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

    private function resetPassword(User $user): string
    {
        $plainPassword = $this->generatePassword();
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
        } catch (Exception $e) {
            $this->error = "{$e->getCode()}::{$e->getMessage()}";
            return '';
        }

        return $plainPassword;
    }

    private function resetApiKey(User $user): string
    {
        $apikey = $this->generateApiKey();
        $user->setApikey($apikey);

        try {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
        } catch (Exception $e) {
            $this->error = "{$e->getCode()}::{$e->getMessage()}";
            return '';
        }

        return $apikey;
    }

    /**
     * Configure the command
     *
     * @return void
     **/
    protected function configure(): void
    {
        $this->setHelp('Reset a Password or API Key for a User')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'The email address of the User'
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'Supported options: [password|apikey]'
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
        $options = ['password', 'apikey'];
        $io = new SymfonyStyle($input, $output);

        $email = trim(strtolower($input->getArgument('email')));
        $token = trim(strtolower($input->getArgument('token')));

        if (!in_array($token, $options)) {
            $io->error('ERROR: Unsupported option!');
            return Command::FAILURE;
        }

        $repo = $this->entityManagerInterface->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]);

        if (null === $user) {
            $io->error('ERROR: User does not exist!');
            return Command::FAILURE;
        }

        if ('password' === $token) {
            $password = $this->resetPassword($user);
            if ('' === $password) {
                $io->error(sprintf('ERROR: %s', $this->error));
                return Command::FAILURE;
            }
            $value = $password;
        } else {
            $apikey = $this->resetApiKey($user);
            if ('' === $apikey) {
                $io->error(sprintf('ERROR: %s', $this->error));
                return Command::FAILURE;
            }
            $value = $apikey;
        }

        $io->success('User updated successfully! Details below:');
        $output->writeln("{$token}: {$value}");
        return Command::SUCCESS;
    }
}
