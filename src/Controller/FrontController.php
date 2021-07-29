<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Pet;
use App\Entity\PetType;
use App\Entity\Planning;
use App\Entity\User;
use App\Form\AnnonceFormType;
use App\Form\ContactFormType;
use App\Form\PetFormType;
use App\Form\PlanningFormType;
use App\Form\UserProfilFormType;
use App\Repository\AnnonceRepository;
use App\Repository\PetRepository;
use App\Repository\PlanningRepository;
use App\Service\MailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(AnnonceRepository $annonceRepository)
    {
        $listAnnonce = $annonceRepository->findByDescId();
        //dd($listAnnonce);

        return $this->render('front/home.html.twig', [
            'listAnnonce'=>$listAnnonce
        ]);
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
    public function profilOthers(User $user, Request $request, AnnonceRepository $annonceRepository, PlanningRepository $planningRepository){

        $post = $request->attributes->get('user');
        $annonces = $annonceRepository->findBy(["master"=> $post->getId()]);
        $plannings = $planningRepository->findBy(["sitter"=> $post->getId()]);
        if($this->getUser() == $post){
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('background/profil_others.html.twig', [
            'user'=>$post,
            'annonces'=>$annonces,
            'plannings'=>$plannings
        ]);
    }

    /**
     * @Route("/profil/settings/{id}", name="app_profil_settings")
     */
    public function profilSettings(User $user, Request $request, EntityManagerInterface $entityManager){

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
    public function contact(Request $request, MailerInterface $mailer){

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $email = (new Email())
                ->from($form->get('mail')->getData())
                ->to(new Address('majdev767@gmail.com', "SitMyPet"))
                ->subject($form->get('need')->getData())
                ->text($form->get('message')->getData());

            $mailer->send($email);
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm'=>$form->createView()
        ]);
    }

    /**
     * @Route("/addpet/{id}", name="addPet")
     * @Security ("is_granted('ROLE_MASTER')")
     */
    public function addPet(User $user, Request $request, EntityManagerInterface  $manager)
    {
        $post = $request->attributes->get('user');
        if($this->getUser() != $post){
            return $this->redirectToRoute("app_profil");
        }

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
     * @Security("is_granted('ROLE_MASTER')")
     */
    public function petSettings(Pet $pet, Request $request,EntityManagerInterface $entityManager){

        $petUser = $pet->getUser()->getId();
        if ($petUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

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
    public function deletePet(Pet $pet, EntityManagerInterface $manager){

        $petUser = $pet->getUser()->getId();
        if ($petUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

        $manager->remove($pet);
        $manager->flush();

        $this->addFlash('success', 'L\'animal a été supprimé');

        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/addAnnonce", name="addAnnonce")
     * @Security("is_granted('ROLE_MASTER')")
     */
    public function addAnnonce(Request $request, EntityManagerInterface $manager){

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
     * * @Security("is_granted('ROLE_MASTER')")
     */
    public function deleteAnnonce(Annonce $annonce, EntityManagerInterface $manager){

        $annonceUser = $annonce->getMaster()->getId();
        if ($annonceUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

        $manager->remove($annonce);
        $manager->flush();

        $this->addFlash('success', 'L\'annonce a été supprimée');

        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/editAnnonce/{id}", name="editAnnonce")
     * @Security("is_granted('ROLE_MASTER')")
     */
    public function editAnnonce(Annonce $annonce, Request $request, EntityManagerInterface $manager){

        $annonceUser = $annonce->getMaster()->getId();
        if ($annonceUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

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
     * @Security("is_granted('ROLE_SITTER')")
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
     * @Security("is_granted('ROLE_SITTER')")
     */
    public function listPlanning(PlanningRepository $planningRepository){


        return $this->render('front/listPlanning.html.twig', [
            'plannings'=>$this->getUser()->getPlannings()
        ]);
    }

    /**
     * @Route("/deleteplanning/{id}", name="deletePlanning/{id}")
     * @Security("is_granted('ROLE_SITTER')")
     */
    public function deletePlanning(Planning $planning, EntityManagerInterface  $entityManager){

        $planningUser = $planning->getSitter()->getId();
        if ($planningUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

        $entityManager->remove($planning);
        $entityManager->flush();

        return $this->redirectToRoute('listPlanning');
    }

    /**
     * @Route("/editPlanning/{id}", name="editPlanning")
     * @Security("is_granted('ROLE_SITTER')")
     */
    public function editPlanning(Request $request, EntityManagerInterface $entityManager, Planning $planning){

        $planningUser = $planning->getSitter()->getId();
        if ($planningUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

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

    public function isDateBetweenDates(\DateTime $userStart, \DateTime $userEnd,  \DateTime $startDate, \DateTime $endDate) {
        return $userStart >= $startDate && $userEnd <= $endDate;
    }

    /**
     * @Route("/searchResults", name="searchResults")
     */
    public function searchResults(Request $request, PlanningRepository $planningRepository, AnnonceRepository $annonceRepository){

        $role = $request->request->get('role');
        $location = $request->request->get('location');
        $dateStart = $request->request->get('start-date');
        $dateEnd = $request->request->get('end-date');
        $budget = $request->request->get('budget');
        $userStart = new \DateTime($dateStart);
        $userEnd = new \DateTime($dateEnd);

        $returnPlanning = [];
        $returnAnnonce =[];
        if($role == 'sitter'){
            $plannings = $planningRepository->findAll();
            foreach ($plannings as $planning){
                $startDate = new \DateTime($planning->getDateStart()->format('Y-m-d'));
                $endDate = new \DateTime($planning->getDateEnd()->format('Y-m-d'));
                if(
                    $this->isDateBetweenDates($userStart, $userEnd, $startDate, $endDate) &&
                    $planning->getSitter()->getTarif()<=$budget &&
                    $planning->getSitter()->getVille() == $location
                )
                {
                    array_push($returnPlanning, $planning);
                }
            }
        }else{
            $annonces = $annonceRepository->findAll();
            foreach ($annonces as $annonce){
                $startDate = new \DateTime($annonce->getDateStart()->format('Y-m-d'));
                $endDate = new \DateTime($annonce->getDateEnd()->format('Y-m-d'));
                if($this->isDateBetweenDates($userStart, $userEnd, $startDate, $endDate)  && $annonce->getMaster()->getVille() == $location){

                    array_push($returnAnnonce, $annonce);
                }
            }
        }

        return $this->render('front/searchResults.html.twig', [
            'role'=>$role,
            'location'=>$location,
            'plannings'=>$returnPlanning,
            'annonces'=>$returnAnnonce

        ]);
    }

    /**
     * @Route("/contactfromannonce/{id}", name="contactFromAnnonce")
     * @throws TransportExceptionInterface
     */
    public function contactFromAnnonce(User $user, MailerInterface $mailer){

        $email = (new Email())
            ->from($user->getEmail())
            ->to(new Address('majdev767@gmail.com', "SitMyPet"))
            ->subject("Quelqu'un souhaites vous contacter")
            ->html(
                $this->renderView('mail/contactfromannonce.html.twig', [
                    'nom'=>$user->getNom(),
                    'prenom'=>$user->getPrenom(),
                    'email'=>$user->getEmail()
                ]), 'text/html'
            );

        $mailer->send($email);

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/profilPet/{id}", name="app_profil_pet")
     * @Security("is_granted('ROLE_MASTER')")
     */
    public function profilPet(Pet $pet, Request $request, PetRepository $petRepository){

        $petUser = $pet->getUser()->getId();
        if ($petUser != $this->getUser()->getId()) {
            return $this->redirectToRoute("app_profil");
        }

        $type = $pet->getType();
        return $this->render('background/profilPet.html.twig',[
            'pet'=>$pet,
            'type'=>$type
        ] );
    }

}
