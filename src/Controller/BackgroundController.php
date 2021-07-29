<?php

namespace App\Controller;

use App\Entity\PetType;
use App\Entity\User;
use App\Form\PetTypeFormType;
use App\Repository\PetTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BackgroundController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function admin()
    {
        return $this->render('/background/admin_home.html.twig');

    }

    /**
     * @Route("admin/addtype", name="addtype")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function addType(PetTypeRepository $petTypeRepository, EntityManagerInterface $manager, Request $request)
    {

        $petType = new PetType();

        $form = $this->createForm(PetTypeFormType::class, $petType);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()):

            $typeIsDefined = $petTypeRepository->findOneBy(['name'=>$form->get('name')->getData()]);

            if(!$typeIsDefined):
                $name = $form->get('name')->getData();
                $petType->setName($name);
                $manager->persist($petType);
                $manager->flush();
            endif;
            $this->addFlash('success', 'Le type a bien été ajouté');

            return $this->redirectToRoute('addtype');

        endif;

        return $this->render("background/addtype.html.twig", [

            'PetTypeForm' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/listuser", name="list_user")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function listUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('/background/list_user.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("admin/deleteuser/{id}", name="delete_user")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function deleteUser(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé');

        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("admin/promoteuser/{id}", name="promote_user")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function promoteUser(User $user, EntityManagerInterface $manager)
    {
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été promu');
        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("admin/demoteuser/{id}/{role}", name="demote_user")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function demoteUser(User $user, EntityManagerInterface $manager, $role)
    {
        if($role == 'master'){
            $user->setRoles(['ROLE_MASTER']);
        }
        else {
            $user->setRoles(['ROLE_SITTER']);;
        }
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été destitué');
        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("admin/changeverify/{id}", name="change_verify")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function changeVerify(User $user, EntityManagerInterface $manager)
    {

        if($user->getSitterVerifie() == false){
            $user->setSitterVerifie(true);
        }else {
            $user->setSitterVerifie(false);
        }
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été modifié');
        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("list_pet_type", name="list_pet_type")
     */
    public function listPetType(PetTypeRepository $petTypeRepository)
    {
        $petTypes = $petTypeRepository->findAll();

        return $this->render('admin/list_pet_type.html.twig', [
            'petTypes'=>$petTypes
        ]);
    }

    /**
     * @Route("admin/delete_type{id}", name="delete_type")
     * @Security("is_granted('ROLE_ADMIN')", message="Access Denied")
     */
    public function deleteType(PetType $petType, EntityManagerInterface $manager)
    {
        $manager->remove($petType);
        $manager->flush();

        $this->addFlash('success', 'Le type a bien été supprimé');

        return $this->redirectToRoute('list_pet_type');
    }

}
