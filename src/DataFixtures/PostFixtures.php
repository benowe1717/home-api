<?php

/**
 * Doctrine Data Fixture for Post Entity
 *
 * PHP version 8.4
 *
 * @category  DataFixture
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Doctrine Data Fixture for Post Entity
 *
 * PHP version 8.4
 *
 * @category  DataFixture
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
class PostFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load data in database
     *
     * @param ObjectManager $manager Persist data to database
     *
     * @return void
     **/
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // $manager->flush();

        $file = './data/posts.csv';

        $row = 1;
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, '|')) !== false) {
                if ($row === 1) {
                    $row++;
                    continue;
                }

                $content = $data[0];
                $author = $data[1];
                $reference = $data[2];

                $post = new Post();
                $post->setContent($content);

                $now = date('Y-m-d H:i:s', time());
                $post->setCreated(new DateTime($now));

                $ref = "user.{$author}";
                $authorRef = $this->getReference($ref, User::class);
                $post->setAuthor($authorRef);

                $manager->persist($post);
                $manager->flush();

                $ref = "post.{$reference}";
                $this->addReference($ref, $post);
            }
        }
    }

    /**
     * Pull in dependent DataFixtures
     *
     * @return List<class-string<FixtureInterface>>
     **/
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
