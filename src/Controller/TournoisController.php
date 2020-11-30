<?php

namespace App\Controller;

use App\Entity\Tournois;
use App\Form\ParticipationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TournoisController extends AbstractController
{
    /**
     * @Route("/tournois", name="tournois")
     */
    public function index(): Response
    {
        $repoTournois = $this->getDoctrine()->getRepository(Tournois::class);
        $toutLesTournois = $repoTournois->findTournoisByDate();



        return $this->render('tournois/index.html.twig', [
            'tournois' => $toutLesTournois,
        ]);
    }

    /**
    * @Route("/tournois/{id}", name="participation_tournoi")
    * 
    */

    public function participation($id, Request $request, ObjectManager $monManager): Response
    {

        $repoTournois = $this->getDoctrine()->getRepository(Tournois::class);
        $tournoisSelectionne = $repoTournois->find($id);
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $message = "";

        $participation = false;

        $participants = $user->getTournois();
        
        foreach( $participants as $value){

            if($value->id == $tournoisSelectionne->id){
                $participation = true;
            }
        }

        if($participation){

            $message = "Vous participez déjà à ce tournoi";

            return $this->render('tournois/participationTournoi.html.twig', [
                'tournoi' => $tournoisSelectionne,
                'message' => $message
            ]);

        }else{
            $form = $this->createForm(ParticipationType::class);
            $form->handleRequest($request); 

            if($form->isSubmitted()){
                $tournoisSelectionne->addUser($user);
                $monManager->flush();
                $message = "Participation prise en compte."; 

                return $this->render('tournois/participationTournoi.html.twig', [
                    'tournoi' => $tournoisSelectionne,
                    'message' => $message
                ]);
            }

            return $this->render('tournois/participationTournoi.html.twig', [
                'tournoi' => $tournoisSelectionne,
                'form' => $form->createView(),
                'message' => $message,
                'participants' => $participants
            ]);
        }
    }
}
