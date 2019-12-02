<?php


namespace App\Controller;


use App\Entity\Genre;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/articles", name="articles")
     */
    public function getArticles(ArticleRepository $repository, Request $request)
    {
//        $p = $request->query->get('p');
//        $queryBuilder = $repository->findAllArticles($p);
//
//
//        return $this->render('admin/genre.html.twig', [
//            'pagination' => $pagination
//        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/genre/new", name="new_genre")
     */
    public function addNewGenre(Request $request)
    {
//        $genre = new Genre();
//
//        $form = $this->createForm(GenreType::class, $genre);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $book = $form->getData();
//            $this->em = $this->getDoctrine()->getManager();
//            $this->em->persist($book);
//            $this->em->flush();
//            $this->get("security.csrf.token_manager")->refreshToken("form_intention");
//
//            $this->addFlash('success', 'Added a new genre!');
//
//            return $this->redirectToRoute("genres");
//        }
//
//        return $this->render('admin/new.html.twig', [
//            'form' => $form->createView(),
//            'title' => 'Add a new genre'
//        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/genre/{id}", name="edit_genre")
     */
    public function editGenre(Request $request, $id)
    {
//        $genre = $this->em->getRepository(Genre::class)->find($id);
//
//        $form = $this->createForm(GenreType::class, $genre);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->em->flush();
//            $this->addFlash('success', 'Genre updated!');
//            return $this->redirectToRoute('genres');
//        }
//
//        return $this->render("main/edit.html.twig", [
//            'form' => $form->createView(),
//            'genre' => $genre,
//            'title' => "Edit genre",
//        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/genre/delete/{id}", name="delete_genre")
     */
    public function deleteGenre(Genre $genre)
    {
//        $this->em->remove($genre);
//        $this->em->flush();
//        $this->addFlash('success', 'Genre deleted!');
//
//        return $this->redirectToRoute('genres');
    }
}