<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Entity\User;
use App\Form\ContactFormType;
use App\Form\PetFormType;
use App\Form\UserProfilFormType;
use App\Security\EmailVerifier;
use App\Service\MailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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
     * @Route("/profil/settings/{id}", name="app_profil_settings")
     */
    public function profilSettings(User $user, Request $request,EntityManagerInterface $entityManager){

        $post = $request->attributes->get('user');
        if($this->getUser() != $post){
            return $this->redirectToRoute("app_profil");
        }

        $form = $this->createForm(UserProfilFormType::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $picture = $form->get('editPicture')->getData();

            if($picture){
                $namePicture = date('YmdHis').uniqid().$picture->getClientOriginalName();

                $picture->move($this->getParameter('upload_directory'), $namePicture);
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

    /**
     * @Route("/contact", name="contact")
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailGenerator $mailGenerator, MailerInterface $mailer){

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){

            dd($form->getData());
            $mail = $form['mail']->getData();
            $subject = $form['need']->getData();
            $message = $form['message']->getData();
            $nom = $form['nom']->getData();
            $prenom = $form['prenom']->getData();

            $mailGenerator->sendMail(
                $mailer,
                'majdev767@gmail.com',
                "$mail",
                "$subject",
                "$message",
                ""
            );
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm'=>$form->createView()
        ]);
    }


    /**
     * @Route("/addpet/{id}", name="addPet")
     */
    public function addPet(User $user, Request $request, EntityManagerInterface  $manager)
    {

        $form=$this->createForm(PetFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $pet = new Pet();
            $pet->setPicture("default.png");
            $pet->setUser($user);
            $pet->setNickname($form->get('nickname')->getData());
            $pet->setDescription($form->get('description')->getData());
            $pet->setAge($form->get('age')->getData());
            $pet->setType($form->get('type')->getData());

            $picture=$form->get('picture')->getData();

            if($picture){
                $namePicture = date('YmdHis').uniqid().$picture->getClientOriginalName();

                $picture->move($this->getParameter('upload_directory'), $namePicture);
                $pet->setPicture($namePicture);
            }


            $manager->persist($pet);
            $manager->flush();

            $this->addFlash('success', "Votre animal a bien été ajouté");
            return $this->redirectToRoute('app_profil');


        }
        return $this->render('background/addPet.html.twig', [
            'petForm'=>$form->createView()
        ]);
    }

}
