<?php


namespace App\Controller;


use App\Entity\Article;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/tables", name="tables")
     */
    public function showTable()
    {
        $repository = $this->em->getRepository(Article::class);
        $tables = $repository->findAll();

        return $this->render('main/images.html.twig', [
            'images' => $tables
        ]);
    }
}