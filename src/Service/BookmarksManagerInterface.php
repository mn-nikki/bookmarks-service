<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Bookmark;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

interface BookmarksManagerInterface
{
    /**
     * @return ObjectRepository
     *                          Возвращает репозиторий сущности Bookmark
     */
    public function getRepository(): ObjectRepository;

    /**
     * @param int $page
     * @param int|null $pageSize
     * @param string|null $orderBy
     * @param string|null $order
     * @return Paginator
     */
    public function pager(int $page = 1, ?int $pageSize = null, ?string $orderBy = null, ?string $order = null): Paginator;

    /**
     * @param string $url
     * @param Bookmark|null $bookmark
     * @return Bookmark
     *                  Возвращает сущность Bookmark с favicon и мета-данными страницы
     */
    public function getBookmarkPageData(string $url, ?Bookmark $bookmark = null): Bookmark;

    /**
     * @param Bookmark $bookmark
     * @return Bookmark
     */
    public function update(Bookmark $bookmark): Bookmark;

    /**
     * @param Bookmark $bookmark
     * @return Bookmark
     */
    public function delete(Bookmark $bookmark): Bookmark;

    /**
     * @param Bookmark $bookmark
     * @return Bookmark
     */
    public function store(Bookmark $bookmark): Bookmark;
}
