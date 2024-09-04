<?php

namespace App\Controller;

use App\Entity\MenuLink;
use App\Entity\PagesList;
use App\Services\PagesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pages')]
class AdminPagesController extends AbstractController
{
    private $pageRepo;
    private $em;
    private $pageService;

    public function __construct(EntityManagerInterface $em, PagesService $pageService, RequestStack $request) {
        $this->em = $em;
        $this->pageRepo = $this->em->getRepository(PagesList::class);
        $this->pageService = $pageService;
    }

    #[Route('/', name: 'admin_pages')]
    public function index(): Response 
    {
        return $this->render('pages/index.html.twig', [
            "pages" => $this->pageRepo->findAll()
        ]);
    }

    /* AJOUTER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/ajout', name: 'admin_pages_add')]
    public function create() 
    {
        // Création du contenu
        $form = $this->pageService->PageManager(true);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->redirectToRoute('admin_pages');
        }

        return $this->render('pages/page-manager.html.twig', [
            'title' => "Ajouter une page",
            'form' => $form->createView(),
        ]);
    }

    /* MODIFIER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/modif/{page_id}', name: 'admin_pages_modify')]
    public function update(string $page_id) 
    {
        // Récupération du lien de la page
        $page = $this->pageRepo->findOneBy(['page_id' => $page_id]);

        // Mise à jour du contenu
        $form = $this->pageService->PageManager(false, $page_id);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_pages_modify', [
                'page_id' => $page_id
            ]);
        }

        return $this->render('pages/page-manager.html.twig', [
            'title' => "Modifier une page",
            'form' => $form->createView(),
            'link' => $page->getPageUrl(),
            'name_fr' => $page->getPageName()[0],
            'pageContent_fr' => htmlspecialchars_decode($page->getPageContent()[0]),
            'metaTitle_fr' => $page->getPageMetaTitle()[0],
            'metaDesc_fr' => $page->getPageMetaDesc()[0],
            'name_en' => $page->getPageName()[1] ?? "",
            'pageContent_en' => htmlspecialchars_decode($page->getPageContent()[1] ?? ""),
            'metaTitle_en' => $page->getPageMetaTitle()[1] ?? "",
            'metaDesc_en' => $page->getPageMetaDesc()[1] ?? "",
        ]);
    }

    /* SUPPRIMER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/suppr/{page_id}', name: 'admin_pages_delete')]
    public function delete(string $page_id) 
    {
        $page = $this->pageRepo->findOneBy(['page_id' => $page_id]);
        if (!$page) {
            throw $this->createNotFoundException("Aucune page n'a été trouvée");
        }

        $menuLinks = $this->em->getRepository(MenuLink::class)->findBy(['page' => $page]);
        foreach ($menuLinks as $link) {
            $this->em->remove($link);
        }

        $this->em->remove($page);
        $this->em->flush();

        return $this->redirectToRoute('admin_pages');
    }
}
