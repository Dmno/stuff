<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
        /**
         * @Route("/register", name="app_register")
         */
        public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
        {
            $user = new User();
            $form = $this->createForm(RegisterType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $user->setRoles(array('ROLE_USER'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login');
            }

            return $this->render('main/login/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }
}