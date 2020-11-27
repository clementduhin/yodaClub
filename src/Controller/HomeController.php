<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\News;
use App\Entity\User;
use App\Form\ContactFormType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $repoNews = $this->getDoctrine()->getRepository(News::class);
        $toutesLesNews = $repoNews->findAll();

        $repoJoueurs = $this->getDoctrine()->getRepository(User::class);
        $top10 = $repoJoueurs->findTop10();
        $classementJoueur = $repoJoueurs->findClassement();
        $user = $this->getUser();

        $rank = 1;        
        if($user){
            $userId = $user->getId();
            foreach ($classementJoueur as $value) {

                
                if($userId == $value->id){
                    break;
                }else{
                    $rank++;
                }
            }
        }

        $mail = new Mail;
        $formContact = $this->createForm(ContactFormType::class, $mail);
        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid()) {

            $emailToAdmin = (new Email())
            ->from("{$mail->getSender()}")
            ->to('testyodaclub@gmail.com')
            ->subject("{$mail->getSubject()}")
            ->text("Voici un mail de demande de contact du site Yoda Club,

Expéditeur: {$mail->getFirstname()} {$mail->getLastname()} - Mail: {$mail->getSender()} 

Message: {$mail->getMessage()}");
        
            $mailer->send($emailToAdmin);

            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'classement' => $top10,
            'news' => $toutesLesNews,
            'formContact' => $formContact->createView(),
            'placeJoueurCo' => $rank
        ]);
    }

    /**
    * @Route("/skahinall", name="skahinall")
    */
    public function skahinall(): Response
    {
        return $this->redirect('http://skahinall.com');
   }
}
