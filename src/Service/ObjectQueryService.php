<?php

/**
 * Symfony Service for quering objects from a database
 *
 * PHP version 8.5
 *
 * @category  Service
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 */

namespace App\Service;

/**
 * Symfony Service for querying objects from a database
 *
 * PHP version 8.5
 *
 * @category  Service
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.2
 * @link      https://github.com/benowe1717/home-api
 */
class ObjectQueryService
{
    private $repository;

    /**
     * ObjectQueryService constructor
     *
     * @param $repository A reference to a specific EntityManagerInterface
     *                    Repository for a specific Entity
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return a count of all objects in the database for the referenced Entity
     *
     * @return int
     */
    private function countObjects(): int
    {
        return $this->repository->countObjects();
    }

    /**
     * Return a count of all objects in the database matching the given
     * search string for the referenced Entity
     *
     * @param string $needle The search string
     *
     * @return int
     */
    private function countFilteredObjects(string $needle): int
    {
        return $this->repository->countFiltered($needle);
    }

    private function countSearchedObjects(array $needles): int
    {
        return $this->repository->countSearched($needles);
    }

    /**
     * Return a paginated array of objects for the referenced Entity
     *
     * @param int $limit  The max number of objects to return
     * @param int $offset The number of objects to skip
     *
     * @return array
     */
    private function getPaginatedObjects(int $limit, int $offset): array
    {
        return $this->repository->getObjectsPaginated($limit, $offset);
    }

    /**
     * Return a paginated array of objects matching the given search string
     * for the referenced Entity
     *
     * @param string $needle The search string
     * @param int    $limit  The max number of objects to return
     * @param int    $offset The number of objects to skip
     *
     * @return array
     */
    private function getPaginatedFilteredObjects(
        string $needle,
        int $limit,
        int $offset
    ): array {
        return $this->repository->getFilteredPaginated($needle, $limit, $offset);
    }

    private function getPaginatedSearchedObjects(
        array $needles,
        int $limit,
        int $offset
    ): array {
        return $this->repository->getSearchedPaginated($needles, $limit, $offset);
    }

    /**
     * Return the paginated list of objects based on the page the user
     * is viewing in the web browser and the pagination service object
     *
     * @param int $currentPage The current page in the web browser
     * @param int $limit       The max number of objects to return
     *
     * @return array [$pagination, $rows]
     */
    public function getObjects(int $currentPage, int $limit = 10): array
    {
        $totalRows = $this->countObjects();

        $pagination = new PaginationService($totalRows, $limit);
        $pagination->setCurrentPage($currentPage);

        $rows = $this->getPaginatedObjects(
            $pagination->getLimit(),
            $pagination->getOffset()
        );

        return ['pagination' => $pagination, 'rows' => $rows];
    }

    /**
     * Return the paginated list of objects matching the given search string
     * based on the page the user is viewing in the web browser
     * and the pagination service object
     *
     * @param string $needle      The search string
     * @param int    $currentPage The current page in the web browser
     * @param int    $limit       The max number of objects to return
     *
     * @return array [$pagination, $rows]
     */
    public function filterObjects(
        string $needle,
        int $currentPage,
        int $limit = 10
    ): array {
        $totalRows = $this->countFilteredObjects($needle);

        $pagination = new PaginationService($totalRows, $limit);
        $pagination->setCurrentPage($currentPage);

        $rows = $this->getPaginatedFilteredObjects(
            $needle,
            $pagination->getLimit(),
            $pagination->getOffset()
        );

        return ['pagination' => $pagination, 'rows' => $rows];
    }

    /**
     * Return the paginated list of objects matching a given set of filters
     * based on the page the user is viewing in the web browser
     * and the pagination service object
     *
     * @param array $needles     The filter(s)
     * @param int   $currentPage The current page in the web browser
     * @param int   $limit       The max number of objects to return
     *
     * @return array [$pagination, $rows]
     */
    public function searchObjects(
        array $needles,
        int $currentPage,
        int $limit = 10
    ): array {
        $totalRows = $this->countSearchedObjects($needles);

        $pagination = new PaginationService($totalRows, $limit);
        $pagination->setCurrentPage($currentPage);

        $rows = $this->getPaginatedSearchedObjects(
            $needles,
            $pagination->getLimit(),
            $pagination->getOffset()
        );

        return ['pagination' => $pagination, 'rows' => $rows];
    }
}
