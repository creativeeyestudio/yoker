<?php

namespace App\Services;

use App\Entity\User;
use App\Form\UserAdminFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService extends AbstractController
{
    private $em;
    private $userRepo;
    private $encoder;

    function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $encoder){
        $this->em = $em;
        $this->userRepo = $this->em->getRepository(User::class);
        $this->encoder = $encoder;
    }

    function getUsersCMS(){
        return $this->userRepo->findAll();
    }

    function getUserCMS(int $id){
        return $this->userRepo->findOneBy(['id' => $id]);
    }

    function updateUser(Request $request, int $id){
        $user = $this->getUserCMS($id);
        $role = $user->getRoles()[0];

        $form = $this->createForm(UserAdminFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $newRole = explode(',', $form->get('roles')->getData());
            $user->setRoles($newRole);

            if ($form->get('remake_pass')->getData() !== null) {
                $newPwd = 'changePassword!!!';
                $password = $this->encoder->hashPassword($user, $newPwd);
                $user->setPassword($password);
            }

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('admin_users_modify',  ['id' => $user->getId()] );
        }

        return $this->render('admin_users/user-manager.html.twig', [
            'form' => $form->createView(),
            'userRole' => $role
        ]);
    }

    function deleteUser(int $id){
        $user = $this->getUserCMS($id);
        $this->em->remove($user);
        $this->em->flush();
        return $user;
    }
}
    