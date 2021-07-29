<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Pet;
use App\Entity\Planning;
use App\Entity\User;
use App\Form\AnnonceFormType;
use App\Form\ContactFormType;
use App\Form\PetFormType;
use App\Form\PlanningFormType;
use App\Form\UserProfilFormType;
use App\Repository\PlanningRepository;
use App\Service\MailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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
        $listpet = $this->getUser()->getAnimaux();
        return $this->render('background/profil.html.twig', [
            'annonces'=>$this->getUser()->getAnnonces(),
            'pets'=>$listpet
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

        $form=$this->createForm(PetFormType::class, null, array('ajout' => true));
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

    /**
     * @Route("/pet/settings/{id}", name="app_pet_settings")
     */
    public function petSettings(Pet $pet, Request $request,EntityManagerInterface $entityManager){

        //dd($pet, $pet->getPicture());
        $form = $this->createForm(PetFormType::class, $pet);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $picture = $form->get('editPicture')->getData();

            if($picture){
                $namePicture = date('YmdHis').uniqid().$picture->getClientOriginalName();

                $picture->move($this->getParameter('upload_directory'), $namePicture);
                $pet->setPicture($namePicture);
            }


            $entityManager->persist($pet);
            $entityManager->flush();
            $this->addFlash('success', "Profil de pet à bien été modifié");
            return $this->redirectToRoute('app_profil');

        }

        return $this->render('front/pet_settings.html.twig', [
            'petEditForm'=>$form->createView(),
            'user'=>$pet
        ]);
    }

    /**
     * @Route("/deletePet/{id}", name="deletePet")
     * @Security ("is_granted('ROLE_MASTER')")
     */
    public function deletePet(Pet $pet, EntityManagerInterface $manager)
    {
        $manager->remove($pet);
        $manager->flush();

        $this->addFlash('success', 'L\'animal a été supprimé');

        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/addAnnonce", name="addAnnonce")
     * @Security("is_granted('ROLE_MASTER')")
     */
    public function addAnnonce(Request $request, EntityManagerInterface $manager)
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setMaster($this->getUser());
            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash('success', "Votre annonce a bien été ajoutée");
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('front/add_annonce.html.twig', [
            'annonceForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/deleteAnnonce/{id}", name="deleteAnnonce")
     */
    public function deleteAnnonce(Annonce $annonce, EntityManagerInterface $manager)
    {
        $manager->remove($annonce);
        $manager->flush();

        $this->addFlash('success', 'L\'annonce a été supprimée');

        return $this->redirectToRoute('app_profil');
    }




    /**
     * @Route("/editAnnonce/{id}", name="editAnnonce")
     */
    public function editAnnonce(Annonce $annonce, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($annonce);
            $manager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('/front/edit_annonce.html.twig',[
            'annonceForm'=>$form->createView()
        ]);

    }

    /**
     * @Route("/addplanning", name="addPlanning")
     */
    public function addPlanning(Request $request, EntityManagerInterface $entityManager, PlanningRepository $planningRepository){

        $form = $this->createForm(PlanningFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $planning = new Planning();
            $planning
                ->setTitle($form->get('title')->getData())
                ->setDateEnd($form->get('date_end')->getData())
                ->setDateStart($form->get('date_start')->getData())
                ->setSitter($this->getUser());

            foreach ($form->get('pet_type')->getData() as $pet){
                $planning->addPetType($pet);
            }

            $entityManager->persist($planning);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('front/addPlanning.html.twig', [
            'planningForm'=>$form->createView()
        ]);
    }

    /**
     * @Route("/listplanning", name="listPlanning")
     */
    public function listPlanning(PlanningRepository $planningRepository){


        return $this->render('front/listPlanning.html.twig', [
            'plannings'=>$this->getUser()->getPlannings()
        ]);
    }

    /**
     * @Route("/deleteplanning/{id}", name="deletePlanning/{id}")
     */
    public function deletePlanning(Planning $planning, EntityManagerInterface  $entityManager){

        $entityManager->remove($planning);
        $entityManager->flush();

        return $this->redirectToRoute('listPlanning');
    }

    /**
     * @Route("/editPlanning/{id}", name="editPlanning")
     */
    public function editPlanning(Request $request, EntityManagerInterface $entityManager, Planning $planning){

        $form = $this->createForm(PlanningFormType::class, $planning);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $planning
                ->setTitle($form->get('title')->getData())
                ->setDateEnd($form->get('date_end')->getData())
                ->setDateStart($form->get('date_start')->getData())
                ->setSitter($this->getUser());

            foreach ($form->get('pet_type')->getData() as $pet){
                $planning->addPetType($pet);
            }

            $entityManager->persist($planning);
            $entityManager->flush();
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('front/addPlanning.html.twig', [
            'planningForm'=>$form->createView()
        ]);

    }

}
