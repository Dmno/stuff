<?php


namespace App\Controller;

use App\Entity\Records;
use App\Entity\Subs;
use App\Form\SenderType;
use App\Repository\RecordsRepository;
use App\Repository\SubsRepository;
use App\Services\SenderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SenderController extends AbstractController
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
     * @Route("/sender", name="sender_page")
     */
    public function mailer(Request $request, RecordsRepository $repository, PaginatorInterface $paginator)
    {
        $amount = $repository->findAll();

        $counter = $paginator->paginate(
            $amount,
            $request->query->getInt('page', 1)/*page number*/,
            10
        );

        return $this->render('main/mailer.html.twig', [
            'counter' => $counter
        ]);
    }

    /**
     * @Route("/sender/upload", name="upload_emails")
     */
    public function uploadEmails(Request $request, SerializerInterface $serializer)
    {
        $record = new Records();
        $form = $this->createForm(SenderType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csv = $form['file']->getData();

            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(',', '"', '\\', '//', true)]);
            $emails = $serializer->decode(preg_replace("/^".pack('H*','EFBBBF')."/", '', file_get_contents($csv)), 'csv');

            foreach ($emails as $email) {
                $record = new Records();

                $record->setEmail($email['email']);
                $record->setName($email['name']);
                $record->setLastname($email['lastname']);

                $this->em->persist($record);
            }
            $this->em->flush();

            $this->get("security.csrf.token_manager")->refreshToken("form_intention");
            $this->addFlash('success', 'Added emails!');
            return $this->redirect($this->generateUrl('sender_page'));
        }

        return $this->render('main/sender.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sender/send", name="send_emails")
     */
    public function sendEmails(SenderService $senderService, SubsRepository $repository)
    {
        $records = $this->em->getRepository(Records::class)->getFirstTen(10);
//        $sub = $repository->findAll();

        foreach ($records as $record) {
            $senderService->sendEmailViaApi($record);

//            $this->em->$repository->incrementAmount();
            $this->em->remove($record);
            }

        $this->addFlash('success', 'Emails sent!');
        $this->em->flush();
        return $this->redirectToRoute('sender_page');
    }
}