<?php

namespace App\Controller;

use App\Entity\PagesList;
use App\Services\PagesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPagesController extends AbstractController
{
    #[Route('/admin/pages', name: 'admin_pages')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $pagesRepo = $entityManager->getRepository(PagesList::class);
        $pages = $pagesRepo->findAll();

        return $this->render('pages/index.html.twig', [
            "pages" => $pages
        ]);
    }

    /* AJOUTER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/ajouter', name: 'admin_pages_add')]
    public function add_page(PagesService $pageService, ManagerRegistry $doctrine, Request $request) {
        
        $form = $pageService->PageManager($doctrine, $request, true);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('pages/add-page.html.twig', [
            'form' => $form->createView()
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
        if (!$pageContentFr)
            $pageContentFr = fopen("../templates/webpages/pages/fr/" . $page_id . ".html.twig", 'w');
            $pageContentFr = file_get_contents("../templates/webpages/pages/fr/" . $page_id . ".html.twig");

        $pageContentEn = file_get_contents("../templates/webpages/pages/en/" . $page_id . ".html.twig");
        if(!$pageContentEn)
            $pageContentEn = fopen("../templates/webpages/pages/en/" . $page_id . ".html.twig", 'w');
            $pageContentEn = file_get_contents("../templates/webpages/pages/en/" . $page_id . ".html.twig");

        // Mise à jour du contenu
        $form = $pageService->PageManager($doctrine, $request, false, $page_id);        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_pages_modify', [
                'page_id' => $page_id
            ]);
        }

        return $this->render('pages/modify-page.html.twig', [
            'form' => $form->createView(),
            'link' => $link,
            'pageContentFr' => $pageContentFr,
            'pageContentEn' => $pageContentEn,
        ]);
    }

    /* SUPPRIMER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/supprimer/{page_id}', name: 'admin_pages_delete')]
    public function delete_page(ManagerRegistry $doctrine, String $page_id) {
        $entityManager = $doctrine->getManager();
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);

        if(!$page) {
            throw $this->createNotFoundException(
                "Aucune page n'a été trouvée"
            );
        }

        $entityManager->remove($page);
        $entityManager->flush();

        // Suppression du fichier
        unlink("../templates/webpages/pages/fr/" . $page_id . ".html.twig");
        unlink("../templates/webpages/pages/en/" . $page_id . ".html.twig");

        return $this->redirectToRoute('admin_pages');
    }
}
