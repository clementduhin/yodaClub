<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/inscription", name="app_registration")
     */
    public function Registration(Request $request, ObjectManager $monManager, UserPasswordEncoderInterface $monEncodeur, MailerInterface $mailer) {
        $user = new User;

        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $toutLesUsers = $repoUser->findAll();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $monEncodeur->encodePassword( $user ,$user->getPassword());           
            $user->setPassword($hashedPassword);
            $user->setValidation(false);
            $monManager->persist($user);
            $monManager->flush();

            $email = (new Email())
                ->from('testyodaclub@gmail.com')
                ->to($user->getEmail())
                ->subject('Inscription au yoda club')
                ->text("Hey {$user->getFirstName()}, nous avons bien reçu ta demande d'inscription au yoda club, tu recevra un mail dès qu'un administrateur aura validé ta demande !");

            $emailToAdmin = (new Email())
                ->from('testyodaclub@gmail.com')
                ->to('testyodaclub@gmail.com')
                ->subject("Demande d'inscription")
                ->text("Nouvelle demande d'inscription de la part de {$user->getFirstName()} {$user->getLastName()} veuillez vous connecter au serveur admin pour valider ");
            
                $mailer->send($email);
                $mailer->send($emailToAdmin);

            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
