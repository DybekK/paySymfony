<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Entity\User;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

        $jsonObject = $serializer->serialize($data, 'json', ['ignored_attributes' => ['users', 'createdAt', 'updatedAt']]);
        
       // return View::create($jsonObject, Response::HTTP_OK);
       return new Response($jsonObject);
    }

    // /**
    //  * @Rest\Post("/register")
    //  * @param Request $request
    //  * @param UserPasswordEncoderInterface $encoder
    //  * @return View
    //  */
    // public function postRegister(Request $request, UserPasswordEncoderInterface $encoder): View
    // {
    //     $em = $this->getDoctrine()->getManager();
    //     $username = $request->get('username');
    //     $email = $request->get('email');
    //     $password = $request->get("password");

    //     if(is_null($username) || is_null($password)) {
    //         return View::create(sprintf('Please verify all your inputs.', Response::HTTP_UNAUTHORIZED));
    //     }

    //     try {
    //         $user = new User();
    //         $user->setUsername($username);
    //         $user->setEmail($email);
    //         $user->setPassword($encoder->encodePassword($user, $password));
    //         $em->persist($user);
    //         $em->flush();
    //     } catch (Exception $e) {
    //         return View::create(sprintf($e), Response::HTTP_CONFLICT);
    //     }

    //     return View::create(sprintf('User %s successfully created', $user->getUsername()), Response::HTTP_OK);
    // }
}
