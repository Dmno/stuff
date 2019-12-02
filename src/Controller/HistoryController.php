<?php


namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**@IsGranted("ROLE_ADMIN")
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/history", name="history_page")
     */
    public function getHistory(HistoryRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $s = $request->query->get('s');
        $queryBuilder = $repository->findAllWithSearchQueryBuilder($s);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('admin/history.html.twig', [
            'pagination' => $pagination
        ]);
    }

//    public function getHistory()
//    {
//        $repository = $this->em->getRepository(History::class);
//        $history = $repository->findAll();
//
//        return $this->render('admin/history.html.twig', [
//            'history' => $history
//        ]);
//    }

    /**
     * @Route("/history/delete/{id}", name="delete_reservation")
     */
    public function deleteHistory(HistoryRepository $historyRepository, BookRepository $bookRepository, $id)
    {
        $history = $historyRepository->findOneById($id);
        $title = $history->getBook();
        $this->em->remove($history);
        $this->em->flush();

        $book = $bookRepository->findOneBy(array('title' => $title));
        $book->setStatus("Available");
        $this->em->flush();

        $this->addFlash('success', 'Reservation deleted!');

        return $this->redirectToRoute('history_page');
    }
}