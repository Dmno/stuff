<?php


namespace App\Controller;

use App\Entity\Records;
use App\Form\SenderType;
use App\Repository\RecordsRepository;
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

            foreach ($emails as $row) {
                $record = new Records();

                $record->setEmail($row['email']);
                $record->setName($row['name']);
                $record->setLastname($row['lastname']);

                $this->em->persist($record);
                $this->em->flush();

            }
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
    public function sendEmails(Request $request, RecordsRepository $repository)
    {
        $record = $this->em->getRepository(Records::class)->getFirstTen(10);

        $b = 0;

        foreach ($record as $row) {
            dd($row[0]);
            $email = $row->email;
            dd($email);
            $name = $row->name;
            $ip = "77.77.77.77";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'http://app.sendloop.com/api/v3/subscriber.subscribe/json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "EmailAddress=".$email."&ListID=1&SubscriptionIP=".$ip."&Fields=".$name);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = 'Apikey: c691-beb4-637a-3e6d-e46a-d8b6-6f03-0ced';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result3 = curl_exec($ch);

            dd($result3);

        }

    }

}