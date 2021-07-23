<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/profil/{username}", name="app_profil_others")
     * @Security("is_granted('ROLE_MASTER') or is_granted('ROLE_SITTER')")
     */
    public function profilOthers(User $user, Request $request){

        $post = $request->attributes->get('user');
        if($this->getUser() == $post){
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('background/profil_others.html.twig', [
            'user'=>$post
        ]);
    }

    /**
     * @Route("/profil/settings/{username}", name="app_profil_settings")
     */
    public function profilSettings(User $user, Request $request, EntityManagerInterface $entityManager){

        $post = $request->attributes->get('user');
        if($this->getUser() != $post){
            return $this->redirectToRoute('app_profil');
        }

        $form = $this->createForm(UserProfilFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $picture = $form->get('profil_picture')->getData();

            if($picture){
                $namePicture = date('YmdHis').uniqid().$picture->getClientOriginalName();

                $picture->move($this->getParameter('upload_directory'), $namePicture);
                unlink($this->getParameter('upload_directory').'/'.$user->getProfilPicture());
                $user->setProfilPicture($namePicture);
            }


            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "Profil à bien été modifié");
            return $this->redirectToRoute('app_profil');

        }

        return $this->render('front/profil_settings.html.twig', [
            'profilEditForm'=>$form->createView(),
            'user'=>$user
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
