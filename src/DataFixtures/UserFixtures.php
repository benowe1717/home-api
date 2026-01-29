<?php

/**
 * Doctrine Data Fixture for User Entity
 *
 * PHP version 8.5
 *
 * @category  DataFixture
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Doctrine Data Fixture for User Entity
 *
 * PHP version 8.5
 *
 * @category  DataFixture
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.2
 * @link      https://github.com/benowe1717/home-api
 */
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * UserFixtures constructor
     *
     * @param UserPasswordHasherInterface $userPasswordHasher The Password Hasher
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Load data in database
     *
     * @param ObjectManager $manager Persist data to database
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $file = './data/users.csv';

        $row = 1;
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row === 1) {
                    $row++;
                    continue;
                }

                $email = $data[0];
                $plainPassword = $data[1];
                $reference = $data[2];

                $user = new User();
                $user->setEmail($email);
                $hashedPassword = $this->userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);

                $bytes = random_bytes(16);
                $apikey = bin2hex($bytes);
                $user->setApikey($apikey);

                $manager->persist($user);
                $manager->flush();

                $ref = "user.{$reference}";
                $this->addReference($ref, $user);
            }
        }
    }
}
