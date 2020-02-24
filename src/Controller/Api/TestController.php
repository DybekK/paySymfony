<?php


namespace App\Controller\Api;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/jwt/test")
     * @return View
     */
    public function getTest(): View
    {
        $user = $this->getUser()->getUsername();
        return View::create($user, Response::HTTP_OK);
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

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $em->persist($user);
        $em->flush();

        return View::create(sprintf('User %s successfully created', $user->getUsername()), Response::HTTP_OK);
    }
}
