<?php


namespace App\Controller;


use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
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
     * @Route("/admin/author", name="authors")
     */
    public function getAuthors(AuthorRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $a = $request->query->get('a');
        $queryBuilder = $repository->findAllAuthors($a);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/author.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/author/new", name="new_author")
     */
    public function addNewAuthor(Request $request)
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book = $form->getData();
            $this->em = $this->getDoctrine()->getManager();
            $this->em->persist($book);
            $this->em->flush();
            $this->get("security.csrf.token_manager")->refreshToken("form_intention");

            $this->addFlash('success', 'Added a new author!');

            return $this->redirectToRoute("authors");
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add a new author'
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/author/{id}", name="edit_author")
     */
    public function editAuthor(Request $request, $id)
    {
        $author = $this->em->getRepository(Author::class)->find($id);

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Author updated!');
            return $this->redirectToRoute('authors');
        }

        return $this->render("main/edit.html.twig", [
            'form' => $form->createView(),
            'author' => $author,
            'title' => "Edit author",
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/author/delete/{id}", name="delete_author")
     */
    public function deleteAuthor(Author $author)
    {
        $this->em->remove($author);
        $this->em->flush();
        $this->addFlash('success', 'Author deleted!');

        return $this->redirectToRoute('authors');
    }
}