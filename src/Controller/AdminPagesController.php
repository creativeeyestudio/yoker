<?php

namespace App\Controller;

use App\Entity\PagesList;
use App\Services\PagesService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPagesController extends AbstractController
{
    #[Route('/admin/pages', name: 'admin_pages')]
    public function index(EntityManagerInterface $em): Response
    {
        $pagesRepo = $em->getRepository(PagesList::class);
        $pages = $pagesRepo->findAll();

        return $this->render('pages/index.html.twig', [
            "pages" => $pages
        ]);
    }

    /* AJOUTER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/ajouter', name: 'admin_pages_add')]
    public function add_page(PagesService $pageService, ManagerRegistry $doctrine, Request $request) {
        
        $title = "Ajouter une page";
        $form = $pageService->PageManager($doctrine, $request, true);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('pages/page-manager.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /* MODIFIER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/modifier/{page_id}', name: 'admin_pages_modify')]
    public function modify_page(PagesService $pageService, ManagerRegistry $doctrine, Request $request, String $page_id) {

        // Récupération du lien de la page
        $entityManager = $doctrine->getManager();
        $link = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id])->getPageUrl();

        // Récupération du contenu de la page
        $pageContentFr = file_get_contents("../templates/webpages/pages/fr/" . $page_id . ".html.twig");
        if (!$pageContentFr) {
            $pageContentFr = '';
            file_put_contents("../templates/webpages/pages/fr/" . $page_id . ".html.twig", $pageContentFr);
        }

        $pageContentEn = file_get_contents("../templates/webpages/pages/en/" . $page_id . ".html.twig");
        if (!$pageContentEn) {
            $pageContentEn = '';
            file_put_contents("../templates/webpages/pages/en/" . $page_id . ".html.twig", $pageContentEn);
        }

        // Mise à jour du contenu
        $title = "Modifier une page";
        $form = $pageService->PageManager($doctrine, $request, false, $page_id);   

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_pages_modify', [
                'page_id' => $page_id
            ]);
        }

        return $this->render('pages/page-manager.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'pageContentFr' => $pageContentFr,
            'pageContentEn' => $pageContentEn,
            'link' => $link,
        ]);
    }

    /* SUPPRIMER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/supprimer/{page_id}', name: 'admin_pages_delete')]
    public function delete_page(ManagerRegistry $doctrine, string $page_id) {
        $entityManager = $doctrine->getManager();
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);

        if(!$page) {
            throw $this->createNotFoundException("Aucune page n'a été trouvée");
        }

        $entityManager->remove($page);
        $entityManager->flush();

        // Suppression du fichier
        $fileFr = "../templates/webpages/pages/fr/" . $page_id . ".html.twig";
        $fileEn = "../templates/webpages/pages/en/" . $page_id . ".html.twig";
        if (file_exists($fileFr)) {
            unlink($fileFr);
        }
        if (file_exists($fileEn)) {
            unlink($fileEn);
        }

        return $this->redirectToRoute('admin_pages');
    }
}
