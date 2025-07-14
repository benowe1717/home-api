<?php

/**
 * Symfony Command for Deleting a User Entity
 *
 * PHP version 8.4
 *
 * @category  Command
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
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

/**
 * Symfony Command for Deleting a User Entity
 *
 * PHP version 8.4
 *
 * @category  Command
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
#[AsCommand(
    name: 'app:delete-user',
    description: 'Delete a User',
    hidden: false
)]
class DeleteUserCommand extends Command
{
    private mixed $error;
    private EntityManagerInterface $entityManagerInterface;

    /**
     * DeleteUserCommand constructor
     *
     * @param EntityManagerInterface $entityManagerInterface The Entity Manager
     **/
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        parent::__construct();
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Delete a User matching the given Email Address
     *
     * @param string $email The Email Address of the User
     *
     * @return bool
     **/
    private function deleteUser(string $email): bool
    {
        $repo = $this->entityManagerInterface->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]);

        if (null === $user) {
            $this->error = 'User does not exist!';
            return false;
        }

        try {
            $this->entityManagerInterface->remove($user);
            $this->entityManagerInterface->flush();
        } catch (Exception $e) {
            $this->error = "{$e->getCode()}::{$e->getMessage()}";
            return false;
        }

        return true;
    }

    /**
     * Configure the command
     *
     * @return void
     **/
    protected function configure(): void
    {
        $this->setHelp('Delete a User')
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

        $result = $this->deleteUser($email);
        if (false === $result) {
            $io->error(sprintf('ERROR: Unable to delete User: %s', $this->error));
            return Command::FAILURE;
        }

        $io->success('User deleted successfully!');
        return Command::SUCCESS;
    }
}
