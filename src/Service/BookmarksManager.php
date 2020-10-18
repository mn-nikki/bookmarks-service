<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Bookmark;
use App\Repository\BookmarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;
use DOMDocument;
use RuntimeException;

class BookmarksManager implements BookmarksManagerInterface
{
    public const DEFAULT_ORDER_PROPERTY = 'id';
    public const DEFAULT_ORDER_DIRECTION = 'asc';
    public const DEFAULT_PAGE_SIZE = 10;

    private BookmarkRepository $repository;
    private EntityManagerInterface $em;
    private ParserInterface $parser;

    /**
     * BookmarksManager constructor.
     * @param BookmarkRepository $repository
     * @param EntityManagerInterface $em
     * @param ParserInterface $parser
     */
    public function __construct(BookmarkRepository $repository, EntityManagerInterface $em, ParserInterface $parser)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->parser = $parser;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function pager(int $page = 1, ?int $pageSize = null, ?string $orderBy = null, ?string $order = null): Paginator
    {
        if (null === $orderBy) {
            $orderBy = self::DEFAULT_ORDER_PROPERTY;
        } elseif (!\array_key_exists($orderBy, \array_flip($this->getProperties()))) {
            throw new RuntimeException(\sprintf('Property \'%s\' not exists in \'%s\'', $orderBy, Bookmark::class));
        }

        $pageSize ??= self::DEFAULT_PAGE_SIZE;
        $order ??= self::DEFAULT_ORDER_DIRECTION;
        $q = $this->repository->createQueryBuilder('p')
            ->orderBy(\sprintf('p.%s', $orderBy), $order)
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize)
        ;

        return new Paginator($q->getQuery());
    }

    private function getProperties(): array
    {
        $properties = (new \ReflectionClass(Bookmark::class))
            ->getProperties(\ReflectionProperty::IS_PRIVATE);

        return array_map(fn (\ReflectionProperty $property) => $property->getName(), $properties);
    }

    /**
     * {@inheritdoc}
     */
    public function getBookmarkPageData(string $url, ?Bookmark $bookmark = null): Bookmark
    {
        $bookmark = $bookmark ?? new Bookmark();
        $bookmark->setUrl($url);

        $output = \file_get_contents($url);
        $document = new DOMDocument();
        @$document->loadHTML($output);

        $bookmark->setDateAdd(new \DateTime());
        $bookmark->setFavicon($this->parser->getFavicon($url));
        $bookmark->setPageTitle($this->parser->getTitle($document));
        $bookmark->setMetaDescription($this->parser->getMetaDescription($document));
        $bookmark->setMetaKeywords($this->parser->getMetaKeywords($document));

        return $bookmark;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Bookmark $bookmark): Bookmark
    {
        $id = $bookmark->getId();

        if (null === $id) {
            throw new RuntimeException(\sprintf('Object with id = \'%s\', was not found', $id));
        }
        $this->flushToStorage($bookmark);
        $this->em->refresh($bookmark);

        return $bookmark;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Bookmark $bookmark): Bookmark
    {
        $id = $bookmark->getId();

        if (null === $id) {
            throw new RuntimeException(\sprintf('Object with id = \'%s\', was not found', $id));
        }
        $this->removeToStorage($bookmark);

        return $bookmark;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Bookmark $bookmark): Bookmark
    {
        $this->flushToStorage($bookmark);
        $this->em->refresh($bookmark);

        return $bookmark;
    }

    /**
     * @param Bookmark $bookmark
     */
    private function flushToStorage(Bookmark $bookmark): void
    {
        try {
            $this->em->persist($bookmark);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param Bookmark $bookmark
     */
    private function removeToStorage(Bookmark $bookmark): void
    {
        try {
            $this->em->remove($bookmark);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
