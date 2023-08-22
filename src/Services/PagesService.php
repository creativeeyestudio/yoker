<?php

namespace App\Services;

use App\Entity\PagesList;
use App\Form\PagesAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PagesService extends AbstractController{
    
    function PageManager(ManagerRegistry $doctrine, Request $request, bool $newPage, string $page_id = null)
    {
        $entityManager = $doctrine->getManager();

        // CREATION / RECUPERATION D'UNE PAGE
        // --------------------------------------------------------
        if ($newPage) {
            $page = new PagesList();
        } else {
            $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
            if (!$page) {
                throw $this->createNotFoundException("Aucune page n'a été trouvée");
            }
        }

        // INITIALISATION DU FORMULAIRE
        // --------------------------------------------------------
        $form = $this->createForm(PagesAdminFormType::class, $page);
        $form->handleRequest($request);

        // ENVOI DU FORMULAIRE
        // --------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $page = $form->getData();

            // Création du nom
            $name = [$form->get('page_name_fr')->getData()];
            $page->setPageName($name);

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($name[0]);
            $slugUrl = $slugify->slugify($form->get('page_url')->getData());

            // Création de l'ID Page
            if ($newPage) {
                $page->setPageId($slugName);
            }

            // Création / Modification de l'URL
            $page->setPageUrl(empty($form->get('page_url')->getData()) ? ($newPage ? $slugName : $page->getPageUrl()) : $slugUrl);

            // Création / Modification du Meta Title
            $metaTitle = [$form->get('page_meta_title_fr')->getData() ?: $name[0]];
            $page->setPageMetaTitle($metaTitle);

            // Création / Modification du Meta Desc
            $metaDesc = [$form->get('page_meta_desc_fr')->getData() ?: ''];
            $page->setPageMetaDesc($metaDesc);

            // Création / Modification du contenu
            $pageContent = [htmlspecialchars($form->get('page_content_fr')->getData()) ?: "Contenu à ajouter"];
            $page->setPageContent($pageContent);

            // Envoi des données vers la BDD
            $entityManager->persist($page);
            $entityManager->flush();
        }

        return $form;
    }
}