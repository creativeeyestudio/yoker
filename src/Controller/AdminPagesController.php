<?php

namespace App\Controller;

use App\Entity\MenuLink;
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
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
        $link = $page->getPageUrl();

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
            'link' => $link,
            'name_fr' => $page->getPageName()[0],
            'metaTitle_fr' => $page->getPageMetaTitle()[0],
            'metaDesc_fr' => $page->getPageMetaDesc()[0],
            'pageContent_fr' => htmlspecialchars_decode($page->getPageContent()[0]),
        ]);
    }

    /* SUPPRIMER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/supprimer/{page_id}', name: 'admin_pages_delete')]
    public function delete_page(ManagerRegistry $doctrine, string $page_id) {
        $em = $doctrine->getManager();
        $page = $em->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
        $menuLink = $em->getRepository(MenuLink::class)->findBy(['page' => $page]);

        if($page) {
            if ($menuLink) {
                foreach($menuLink as $link){
                    $em->remove($link);
                }
            }
            $em->remove($page);
            $em->flush();
        } else {
            throw $this->createNotFoundException("Aucune page n'a été trouvée");
        }

        return $this->redirectToRoute('admin_pages');
    }
}
