<?php

namespace App\DataFixtures;

use App\Service\BookmarksManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private BookmarksManagerInterface $bookmarksManager;

    /**
     * AppFixtures constructor.
     * @param BookmarksManagerInterface $bookmarksManager
     */
    public function __construct(BookmarksManagerInterface $bookmarksManager)
    {
        $this->bookmarksManager = $bookmarksManager;
    }

    private array $urls = [
        'https://vk.com/',
        'https://habrahabr.ru/',
        'https://comnews.ru/',
        'https://appleinsider.ru/',
        'https://ferra.ru/',
        'https://3dnews.ru/',
        'https://roem.ru/',
        'https://mail.ru/',
        'https://cnews.ru/',
        'https://www.google.com/',
        'https://yandex.ru/',
        'https://symfony.com/',
        'https://github.com/',
        'https://www.netflix.com/',
        'https://webformatter.com/',
        'https://www.youtube.com/',
        'https://wikium.ru/',
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->urls as $url) {
            $headers = \get_headers($url);
            if (false === \strpos($headers[0], '200')) {
                continue;
            }

            $bookmark = $this->bookmarksManager->getBookmarkPageData($url);
            $manager->persist($bookmark);
        }
        $manager->flush();
    }
}
