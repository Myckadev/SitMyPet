<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * @Route("/profil", name="app_profil")
     * @Security("is_granted('ROLE_MASTER') or is_granted('ROLE_SITTER')")
     */
    public function profil(){

        return $this->render('background/profil.html.twig', [

        ]);
    }

    /**
     * @Route("/confidentiality", name="confidentiality")
     */
    public function polconf()
    {
        return $this->render('front/confidentiality.html.twig');
    }


    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgv()
    {
        return $this->render('front/cgv.html.twig');
    }

}
