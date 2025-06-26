<?php

/**
 * Symfony Service for paginating results from a database query
 *
 * PHP version 8.4
 *
 * @category  Service
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Service;

/**
 * Symfony Service for paginating results from a database query
 *
 * PHP version 8.4
 *
 * @category  Service
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
class PaginationService
{
    private ?int $totalRecords = null;

    private ?int $limit = null;

    private ?int $currentPage = null;

    private ?int $pages = null;

    private ?int $offset = null;

    private ?int $next = null;

    private ?int $previous = null;

    /**
     * PaginationService constructor
     *
     * @param int $records The total number of objects to paginate
     * @param int $limit   The maximum number of objects to display
     **/
    public function __construct(int $records, int $limit = 10)
    {
        $this->totalRecords = $records;
        $this->limit = $limit;
        $this->pages = ceil($this->totalRecords / $this->limit);
    }

    /**
     * $totalRecords getter
     *
     * @return ?int
     **/
    public function getTotalRecords(): ?int
    {
        return $this->totalRecords;
    }

    /**
     * $limit getter
     *
     * @return ?int
     **/
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * $currentPage getter
     *
     * @return ?int
     **/
    public function getCurrentPage(): ?int
    {
        return $this->currentPage;
    }

    /**
     * $pages getter
     *
     * @return ?int
     **/
    public function getPages(): ?int
    {
        return $this->pages;
    }

    /**
     * $offset getter
     * Returns 0 if $currentPage = 1, returns a multiple of $limit if
     * $currentPage > 1
     *
     * @return ?int
     **/
    public function getOffset(): ?int
    {
        $this->offset = 0;
        if ($this->currentPage > 1) {
            $this->offset = ($this->currentPage * $this->limit) - $this->limit;
        }
        return $this->offset;
    }

    /**
     * $next getter
     * Returns $currentPage + 1
     *
     * @return ?int
     **/
    public function getNext(): ?int
    {
        $this->next = $this->currentPage + 1;
        return $this->next;
    }

    /**
     * $previous getter
     * Returns $currentPage - 1
     *
     * @return ?int
     **/
    public function getPrevious(): ?int
    {
        $this->previous = $this->currentPage - 1;
        return $this->previous;
    }

    /**
     * $currentPage setter
     *
     * @param int $page The current page of results in the web browser
     *
     * @return static
     **/
    public function setCurrentPage(int $page): static
    {
        $this->currentPage = $page;

        return $this;
    }
}
