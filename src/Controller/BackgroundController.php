<?php

namespace App\Controller;

use App\Entity\PetType;
use App\Entity\User;
use App\Form\PetTypeFOrmType;
use App\Repository\PetTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function addType(PetTypeRepository $petTypeRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request)
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
     * @Route("/listuser", name="list_user")
     */
    public function listUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('/background/list_user.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("/deleteuser/{id}", name="delete_user")
     */
    public function deleteUser(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé');

        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("/promoteuser/{id}", name="promote_user")
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
     * @Route("/demoteuser/{id}/{role}", name="demote_user")
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
     * @Route("/changeverify/{id}", name="change_verify")
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

}
