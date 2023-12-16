<?php

namespace App\Controller;

use App\Entity\CodeWeave;
use App\Form\CodeWeaveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExtCodeWeaveController extends AbstractController
{
    private $em;
    private $repo;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(CodeWeave::class);
    }

    #[Route('/admin/codeweave', name: 'codeweave')]
    public function index(): Response
    {
        $cssList = $this->repo->findBy(['type' => 0]);
        $jsList = $this->repo->findBy(['type' => 1]);

        return $this->render('ext_codeweave/index.html.twig', [
            'cssList' => $cssList,
            'jsList' => $jsList
        ]);
    }

    #[Route(path: '/admin/codeweave/ajouter', name: 'codeweave_create_file')]
    public function create(Request $request): Response
    {
        $code = new CodeWeave();
        $form = $this->createForm(CodeWeaveType::class, $code);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->em->persist($code);
            $this->em->flush();

            return $this->redirectToRoute('codeweave');
        }

        return $this->render('ext_codeweave/form-manager.html.twig', [
            'title' => "Créer un nouveau fichier",
            'form' => $form->createView(),
        ]);
    }
}
