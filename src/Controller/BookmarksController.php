<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\BookmarksManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;

class BookmarksController extends AbstractController
{
    private BookmarksManagerInterface $bookmarksManager;

    /**
     * BookmarksController constructor.
     * @param BookmarksManagerInterface $bookmarksManager
     */
    public function __construct(BookmarksManagerInterface $bookmarksManager)
    {
        $this->bookmarksManager = $bookmarksManager;
    }

    /**
     * @Route("/bookmarks/{page<\d+>?1}", name="bookmarks_list")
     * @param int|null $page
     * @return Response
     */
    public function index(?int $page = null)
    {
        /*
        $urls = [
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
        ];

        foreach ($urls as $url)
        {
            $headers = \get_headers($url);
            if(\strpos($headers[0], '200') === false) continue;
            $bookmarks[] = $this->bookmarksManager->getBookmarkPageData($url);
        }

        dd($bookmarks) //debug
        */

        $bookmarks = $this->bookmarksManager->pager($page, 5);

        return $this->render('bookmarks/index.html.twig', [
            'controller_name' => 'BookmarksController',
            'data' => $bookmarks,
        ]);
    }
}
