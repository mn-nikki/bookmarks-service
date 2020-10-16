<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookmarksController extends AbstractController
{
    /**
     * @Route("/bookmarks", name="bookmarks")
     */
    public function index()
    {
        return $this->render('bookmarks/index.html.twig', [
            'controller_name' => 'BookmarksController',
        ]);
    }
}
