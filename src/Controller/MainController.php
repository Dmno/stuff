<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function main()
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/articles", name="articles_page")
     */
    public function articles()
    {
        return $this->render('main/articles.html.twig');
    }

    /**
     * @Route("/images", name="images_page")
     */
    public function images()
    {
        return $this->render('main/images.html.twig');
    }

    /**
     * @Route("/tables", name="tables_page")
     */
    public function tables()
    {
        return $this->render('main/tables.html.twig');
    }

    /**
     * @Route("/books", name="books_page")
     */
    public function books()
    {
        return $this->render('main/books.html.twig');
    }

}