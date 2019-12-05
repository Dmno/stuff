<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\BookRepository;
use App\Repository\HistoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
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
     * @Route("/admin", name="admin")
     */
    public function getusers(UserRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $u = $request->query->get('u');
        $queryBuilder = $repository->findAllUsers($u);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('admin/admin.html.twig', [
            'pagination' => $pagination
        ]);
    }

//    /**
//     * @Route("/admin/edit/{id}", name="edit_user")
//     */
//    public function editUser(Request $request, $id)
//    {
//        $repository = $this->em->getRepository(User::class);
//        $users = $repository->findBy(['id' => $id]);
//
//        $user = $this->em->getRepository(User::class)->find($id);
//
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->em->flush();
//            $this->addFlash('success', 'User updated!');
//            return $this->redirectToRoute('admin');
//        }
//
//        return $this->render("main/edit.html.twig", [
//            'form' => $form->createView(),
//            'users' => $users
//        ]);
//    }


    /**
     * @Route("/admin/delete/{id}", name="delete_user")
     */
    public function deleteUser(User $user, HistoryRepository $historyRepository, UserRepository $userRepository, BookRepository $bookRepository, $id)
    {
        $user = $userRepository->findOneById($id);
        $username = $user->getUsername();
        $history = $historyRepository->findBy(['user' => $username]);

        foreach ($history as $row) {
            $book = $row->getBook();
            $results = $bookRepository->findBy(['title' => $book, 'status' => true]);
            foreach ($results as $result) {
                $result->setStatus(false);
                $this->em->flush();
            }
            $this->em->remove($row);
            $this->em->flush();
        }

        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('success', 'User deleted!');

        return $this->redirectToRoute('admin');
    }



//    /**
//     * @IsGranted("ROLE_USER")
//     * @Route("/books/reserve/{id}", name="reserve_book")
//     */
//    public function reserveBook(BookRepository $bookRepository, $id)
//    {
//        $book = $bookRepository->findOneById($id);
//        $title = $book->getTitle();
//        $user = $this->getUser();
//        $name = $user->getUsername();
//
//        $history = new History();
//
//        $history->setUser($name);
//        $history->setBook($title);
//        $history->setAction("Reserved");
//
//        $this->em->persist($history);
//        $this->em->flush();
//
//        $book->setStatus("Reserved");
//        $this->em->flush();
//
//        $this->addFlash('success', 'Book reserved!');
//
//        return $this->redirectToRoute("books_page");
//    }
//
//}


}