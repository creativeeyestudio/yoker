<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuLink;
use App\Form\NavCreateFormType;
use App\Form\NavLinksFormType;
use App\Form\NavSelectFormType;
use App\Form\NavUpdateLinkFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminNavigationController extends AbstractController
{
    private function navLinksForm(ManagerRegistry $doctrine, Request $request, int $id_menu) {
        $nav_links_form = $this->createForm(NavLinksFormType::class);
        $nav_links_form->handleRequest($request);

        if ($nav_links_form->isSubmitted() && $nav_links_form->isValid()) {
            $em = $doctrine->getManager();
            $menu = $em->getRepository(Menu::class)->findOneBy(['pos' => $id_menu]);

            $pagesToInsert = [];
            $postsToInsert = [];
            $cusName = $nav_links_form->get('cus_name')->getData();
            $cusLink = $nav_links_form->get('cus_link')->getData();

            // Collect pages and posts data to be inserted
            $pages = $nav_links_form->get('pages')->getData();
            $posts = $nav_links_form->get('posts')->getData();

            foreach ($pages as $link) {
                $oldLink = $em->getRepository(MenuLink::class)->findOneBy([
                    'menu' => $menu->getId(),
                    'page' => $link
                ]);

                if (!$oldLink) {
                    $pagesToInsert[] = $link;
                }
            }

            foreach ($posts as $link) {
                $oldLink = $em->getRepository(MenuLink::class)->findOneBy([
                    'menu' => $id_menu,
                    'post' => $link
                ]);

                if (!$oldLink) {
                    $postsToInsert[] = $link;
                }
            }

            // Combine insertions
            $menuLinksToInsert = [];

            foreach ($pagesToInsert as $link) {
                $menuLink = new MenuLink();
                $menuLink->setMenu($menu);
                $menuLink->setCusName($link->getPageName());
                $menuLink->setPage($link);
                $menuLink->setOrderLink(0);
                $menuLinksToInsert[] = $menuLink;
            }

            foreach ($postsToInsert as $link) {
                $menuLink = new MenuLink();
                $menuLink->setMenu($menu);
                $menuLink->setCusName($link->getPostName());
                $menuLink->setPost($link);
                $menuLink->setOrderLink(0);
                $menuLinksToInsert[] = $menuLink;
            }

            // Insert custom link if both name and link are provided
            if ($cusName && $cusLink) {
                $menuLink = new MenuLink();
                $menuLink->setOrderLink(0);
                $menuLink->setMenu($menu);
                $menuLink->setCusName([$cusName]);
                $menuLink->setCusLink($cusLink);
                $menuLinksToInsert[] = $menuLink;
            }

            // Batch insert all menu links
            foreach ($menuLinksToInsert as $menuLink) {
                $em->persist($menuLink);
            }

            $em->flush();
        }

        return $nav_links_form;
    }

    private function navSelectForm(Request $request) {
        $nav_select_form = $this->createForm(NavSelectFormType::class);
        $nav_select_form->handleRequest($request);

        if ($nav_select_form->isSubmitted() && $nav_select_form->isValid()) { 
            $id_menu = $nav_select_form->get('nav_select')->getData()->getId();
        }

        return $nav_select_form;
    }

    private function navCreateForm(ManagerRegistry $doctrine, Request $request) {
        $menu = new Menu();
        $nav_create_form = $this->createForm(NavCreateFormType::class, $menu);
        $nav_create_form->handleRequest($request);
        if ($nav_create_form->isSubmitted() && $nav_create_form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($menu);
            $entityManager->flush();
        }

        return $nav_create_form;
    }

    public function initPage(ManagerRegistry $doctrine, Request $request, string $title = '', int $id_menu = 0) {
        $nav_links_form = $this->navLinksForm($doctrine, $request, $id_menu);
        $nav_select_form = $this->navSelectForm($request);
        $nav_create_form = $this->navCreateForm($doctrine, $request);

        $em = $doctrine->getManager();
        $menusBase = $em->getRepository(Menu::class);
        $menus = $menusBase->findAll();
        $menu = $menusBase->findOneBy(['pos' => $id_menu]);
        
        $route_name = $request->attributes->get('_route');

        return $this->render('admin_navigation/index.html.twig', [
            'nav_form' => $nav_links_form->createView(),
            'menu_form' => $nav_select_form->createView(),
            'menu_create_form' => $nav_create_form->createView(),
            'title' => $title,
            'id_menu' => $id_menu,
            'menus' => $menus,
            'menu' => $menu,
            'route_name' => $route_name
        ]);
    }

    #[Route('/admin/navigation', name: 'app_admin_nav')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $render = $this->initPage($doctrine, $request, "Navigation du site");
        return $render;
    }

    #[Route('/admin/navigation/menu', name: 'app_admin_nav_menu')]
    public function createMenu(ManagerRegistry $doctrine, Request $request): Response
    {
        $render = $this->initPage($doctrine, $request, "Créer un nouveau menu");
        return $render;
    }

    #[Route('/admin/navigation/{id_menu}', name: 'app_admin_nav_select')]
    public function navSelected(ManagerRegistry $doctrine, Request $request, int $id_menu): Response
    {
        $render = $this->initPage($doctrine, $request, "Navigation du site", $id_menu);
        return $render;
    }

    #[Route('/admin/navigation/manage-link/{id_link}', name: 'app_admin_nav_manage_link')]
    public function manageLink(ManagerRegistry $doctrine, Request $request, int $id_link){
        $em = $doctrine->getManager();
        $menuLink = $em->getRepository(MenuLink::class)->find($id_link);
        $form = $this->createForm(NavUpdateLinkFormType::class, $menuLink);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $name = [
                $form->get('cus_name')->getData()
            ];
            $link = [
                $form->get('cus_link')->getData()
            ];
            $em = $doctrine->getManager();
            $menuLink->setCusName($name);
            $menuLink->setCusLink($link);
            $em->persist($menuLink);
            $em->flush();
        }
        
        return $this->render('admin_navigation/popup.html.twig', [
            'form' => $form,
            'name' => $menuLink->getCusName(),
            'link' => $menuLink->getCusLink(),
        ]);
    }

    #[Route(path: '/delete-nav', name: 'delete_link', methods: ['POST'])]
    public function deleteNav(ManagerRegistry $doctrine, Request $request){ 
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $menu = $em->getRepository(Menu::class)->findOneBy(['id' => $data]);
        $menuItems = $em->getRepository(MenuLink::class)->findBy(['menu' => $menu->getId()]);

        foreach ($menuItems as $item) {
            if ($item) {
                $em->remove($item);
            }
        }

        $em->remove($menu);
        $em->flush();
    }

    #[Route(path: '/order-nav-link', name: 'order_nav_link', methods: ['POST'])]
    public function orderNavLink(ManagerRegistry $doctrine, Request $request){
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        foreach ($data as $item) {
            $link = $em->getRepository(MenuLink::class)->findOneBy(['id' => $item['id']]);
            if ($link) {
                if ($item['sublist']) {
                    $sublist = $em->getRepository(MenuLink::class)->findOneBy(['id' => $item['sublist']]);
                    $link->setParent($sublist);
                }
                $link->setOrderLink($item['order']);
                $em->persist($link);
            }
        }
        
        $em->flush();
        return new JsonResponse(['message' => 'Ordre des liens enregistré avec succès.']);
    }

    #[Route(path: '/delete-nav-link', name: 'delete_nav_link', methods: ['POST'])]
    public function deleteNavLink(ManagerRegistry $doctrine, Request $request) {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $link = $em->getRepository(MenuLink::class)->findOneBy(['id' => $data]);
        $em->remove($link);
        $em->flush();

        return new JsonResponse(['message' => 'Ordre des liens enregistré avec succès.']);
    }

}
