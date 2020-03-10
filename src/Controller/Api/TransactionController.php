<?php

namespace App\Controller\Api;

use App\Entity\Kind;
use App\Entity\Transaction;
use App\Entity\User;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/jwt/transactions")
     * @param Request $request
     * @return View
     */
    public function getTransactions(Request $request): Response
    {
        $time = $request->query->get('time');

        switch ($time) {
            case 'week':
                $start_date =  new \DateTime('monday this week');
                $end_date = new \DateTime('monday next week');
            break;
            case 'month':
                $start_date =  new \DateTime('midnight first day of this month');
                $end_date = new \DateTime('last day of this month');
            break;
            case 'year':
                $start_date =  new \DateTime('first day of January');
                $end_date = new \DateTime('last day of December');
            break;
            default:
                return 0;
        }

        $id = $this->getUser()->getId();
        $data = $this->getDoctrine()->getManager()->getRepository(Transaction::class)->findByDate($id, $start_date, $end_date);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonObject = $serializer->serialize($data, 'json');
        
       // return View::create($jsonObject, Response::HTTP_OK);
       return new Response($jsonObject);
    }

    /**
     * @Rest\Post("/jwt/transaction")
     * @param Request $request
     * @return View
     */
    public function postTransaction(Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        $transactionname = $request->get('transactionname');
        $amount = $request->get('amount');
        $type = $request->get("type");
        $id = $this->getUser()->getId();

        if(is_null($transactionname) || is_null($amount)) {
            return View::create(sprintf('Please verify all your inputs.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $transaction = new Transaction();
            $user = $em->getRepository(User::class)->find($id);
            $kind = $em->getRepository(Kind::class)->findOneBy(['kindname' => 'Any']);

            $transaction->setTransactionname($transactionname);
            $transaction->setAmount($amount);
            $transaction->setType($type);

            $user->addTransaction($transaction);
            $transaction->addUser($user);

            $kind->addTransaction($transaction);
            $transaction->setKind($kind);

            $em->persist($kind);
            $em->persist($transaction);
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            return View::create(sprintf($e), Response::HTTP_CONFLICT);
        }

        return View::create(sprintf('Transaction successfully created'), Response::HTTP_OK);
    }

      /**
     * @Rest\Delete("/jwt/transaction/{id}")
     * @return View
     */
    public function deleteTransaction($id): View
    {
        $em = $this->getDoctrine()->getManager();

        if(is_null($id)) {
            return View::create(sprintf('Please verify all your inputs.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $transactionItem = $em->getRepository(Transaction::class)->find($id);
            $em->remove($transactionItem);
            $em->flush();   
        } catch (Exception $e) {
            return View::create(sprintf($e), Response::HTTP_CONFLICT);
        }

        return View::create(sprintf('Transaction has been successfully deleted'), Response::HTTP_OK);
    }
}