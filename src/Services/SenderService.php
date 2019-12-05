<?php


namespace App\Services;


use App\Entity\Records;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SenderService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function sendEmailViaApi(Records $records)
    {
            $httpClient = HttpClient::create(
                [
                    'headers' => ['APIKey' => 'eec3-27a9-3081-fa2c-b972-17cc-da34-46b2']
                ]);

            try {
                $response = $httpClient->request('POST', 'http://app.sendloop.com/api/v3/subscriber.subscribe/json',
                    [
                        "body" => "EmailAddress=" . $records->getEmail() . "&ListID=1&SubscriptionIP=77.77.77.77&Fields=".$records->getName()
                    ]);

                $response = json_decode($response->getContent());

            } catch (TransportExceptionInterface $e) {
                echo 'Message: ' . $e->getMessage();
            }
            return $response;
    }
}