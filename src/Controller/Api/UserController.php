<?php

namespace App\Controller\Api;

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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/jwt/test")
     * @return View
     */
    public function getTest(): View
    {
        // $user = $this->getUser()->getUsername();
        $user = $this->getUser()->getTransactions();

        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

// Serialize your object in Json
        $jsonObject = $serializer->serialize($user, 'json', ['ignored_attributes' => ['users', 'createdAt']]);
        
        return View::create($jsonObject, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return View
     */
    public function postRegister(Request $request, UserPasswordEncoderInterface $encoder): View
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get("password");

        if(is_null($username) || is_null($password)) {
            return View::create(sprintf('Please verify all your inputs.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($encoder->encodePassword($user, $password));
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            return View::create(sprintf($e), Response::HTTP_CONFLICT);
        }

        return View::create(sprintf('User %s successfully created', $user->getUsername()), Response::HTTP_OK);
    }
}
