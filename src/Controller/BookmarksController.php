<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bookmark;
use App\Form\BookmarkType;
use App\Service\BookmarksManagerInterface;
use App\Service\UrlValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookmarksController extends AbstractController
{
    private BookmarksManagerInterface $bookmarksManager;
    private UrlValidatorInterface $urlValidator;

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
    public function index(?int $page = null): Response
    {
        $request = Request::createFromGlobals();
        $orderBy = $request->query->get('orderBy');
        $order = $request->query->get('order');

        $bookmarks = $this->bookmarksManager->pager($page, $pageSize = 5, $orderBy, $order);
        $maxPage = ceil(count($bookmarks) / $pageSize);

        return $this->render('bookmarks/index.html.twig', [
            'data' => $bookmarks,
            'page' => $page,
            'maxPage' => $maxPage,
            'orderBy' => $orderBy,
            'order' => $order,
        ]);
    }

    /**
     * @Route(path="/bookmarks/view/{id}", name="bookmark_view" ,requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     */
    public function view(int $id): Response
    {
        $bookmark = $this->bookmarksManager->getRepository()->find($id);

        return $this->render('bookmarks/view.html.twig', [
            'bookmark' => $bookmark,
        ]);
    }

    /**
     * @Route(path="/bookmarks/remove/{id}", name="bookmark_remove", requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     */
    public function remove(int $id): Response
    {
        $bookmark = $this->bookmarksManager->getRepository()->find($id);
        $this->bookmarksManager->delete($bookmark);

        return $this->redirectToRoute('bookmarks_list');
    }

    /**
     * @Route(path="/bookmarks/new", name="bookmark_create")
     * @param Request $request
     * @param UrlValidatorInterface $urlValidator
     * @return Response
     */
    public function create(Request $request, UrlValidatorInterface $urlValidator): Response
    {
        $bookmark = new Bookmark();
        $form = $this->createForm(BookmarkType::class, $bookmark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookmark = $form->getData();
            $url = $bookmark->getUrl();
            $errors = $urlValidator->validateUrl($url);

            $hasUrl = !empty($this->bookmarksManager->getRepository()->findBy(['url' => $url]));

            if ($hasUrl) {
                $errors->hasUrl = \sprintf('Url with address \'%s\' already exists', $url);
            }

            if (null !== $errors) {
                return $this->render('form/new.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                ]);
            }

            $bookmark = $this->bookmarksManager->getBookmarkPageData($url, $bookmark);
            $this->bookmarksManager->store($bookmark);

            return $this->redirectToRoute('bookmark_view', ['id' => $bookmark->getId()]);
        }

        return $this->render('form/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
