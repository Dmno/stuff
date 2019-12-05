<?php


namespace App\Controller;

use App\DBAL\Types\BookStatusType;
use App\Entity\Author;
use App\Entity\Genre;
use App\Entity\History;
use App\Form\BookType;
use App\Form\UploadType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

//    Regular fetch

//    /**
////     * @Route("/books", name="books")
////     */
////    public function getBooks()
////    {
////        $repository = $this->em->getRepository(Book::class);
////        $books = $repository->findAll();
////
////        return $this->render('main/books.html.twig', [
////            'books' => $books
////        ]);
////    }

// Just search fetch

//    /**
//     * @Route("/books", name="books")
//     */
//    public function getBooks(BookRepository $repository, Request $request)
//    {
//        $q = $request->query->get('q');
//        $books = $repository->findAllWithSearch($q);
//
//        return $this->render('main/books.html.twig', [
//            'books' => $books
//        ]);
//    }

// Paginator fetch

    /**
     * @Route("/books", name="books")
     */
    public function getBooks(BookRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $q = $request->query->get('q');
        $queryBuilder = $repository->findAllBooksWithSearchQuery($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('main/books.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/books/new", name="new_book")
     */
    public function addNewBook(Request $request)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book = $form->getData();
            $this->em = $this->getDoctrine()->getManager();
            $book->setStatus(BookStatusType::AVAILABLE);
            $this->em->persist($book);
            $this->em->flush();
            $this->get("security.csrf.token_manager")->refreshToken("form_intention");

            $this->addFlash('success', 'Added a new book!');

            return $this->redirectToRoute("books_page");
        }

        return $this->render('books/new_book.html.twig', [
            'form' => $form->createView(),
            'title' => "Add a new book"
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/books/upload", name="upload_books")
     */
    public function uploadBooks(Request $request, SerializerInterface $serializer)
    {
        $book = new Book();
        $form = $this->createForm(UploadType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csv = $form['file']->getData();

            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(',', '"', '\\', '//', true)]);
            $contacts = $serializer->decode(preg_replace("/^".pack('H*','EFBBBF')."/", '', file_get_contents($csv)), 'csv');

            foreach ($contacts as $row) {

                $book = new Book();

                $book->setTitle($row['title']);
                $book->setAuthor($row['author']);
                $book->setReleased($row['released']);
                $book->setGenre($row['genre']);
                $book->setStatus(BookStatusType::AVAILABLE);

                $this->em->persist($book);

                $repository = $this->em->getRepository(Author::class);
                $authorExists = $repository->findOneBy(['fullName' => $row['author']]);

                if (empty($authorExists)) {
                    $author = new Author();

                    $author->setFullName($row['author']);
                    $this->em->persist($author);
                    $this->em->flush();
                }

                $repository = $this->em->getRepository(Genre::class);
                $genreExists = $repository->findOneBy(['title' => $row['genre']]);

                if (empty($genreExists)) {
                    $genre = new Genre();

                    $genre->setTitle($row['genre']);
                    $this->em->persist($genre);
                    $this->em->flush();
                }
            }
                $this->em->flush();
                $this->get("security.csrf.token_manager")->refreshToken("form_intention");
                $this->addFlash('success', 'Started adding books!');
                 return $this->redirect($this->generateUrl('books_page'));
        }

        return $this->render('admin/upload.html.twig', [
            'title' => 'Upload a csv file',
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/books/edit/{id}", name="edit_book")
     */
    public function editArticle(Request $request, $id)
    {
        $repository = $this->em->getRepository(Book::class);
        $books = $repository->findBy(['id' => $id]);

        $book = $this->em->getRepository(Book::class)->find($id);
//        dd($book);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Book updated!');
            return $this->redirectToRoute('books');
        }

        return $this->render("main/edit.html.twig", [
            'form' => $form->createView(),
            'books' => $books,
            'title' => "Edit book"
        ]);

    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/books/delete/{id}", name="delete_book")
     */
    public function deleteBook(Book $book)
    {
        $this->em->remove($book);
        $this->em->flush();
        $this->addFlash('success', 'Book deleted!');

        return $this->redirectToRoute('books');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/books/reserve/{id}", name="reserve_book")
     */
    public function reserveBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->findOneById($id);
        $title = $book->getTitle();
        $user = $this->getUser();
        $name = $user->getUsername();

        $history = new History();

        $history->setUser($name);
        $history->setBook($title);
        $history->setAction(true);

        $this->em->persist($history);
        $this->em->flush();

        $book->setStatus("Reserved");
        $this->em->flush();

        $this->addFlash('success', 'Book reserved!');

        return $this->redirectToRoute("books_page");
    }
}

