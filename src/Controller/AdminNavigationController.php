<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuLink;
use App\Form\NavCreateFormType;
use App\Form\NavLinksFormType;
use App\Form\NavSelectFormType;
use App\Form\NavUpdateLinkFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminNavigationController extends AbstractController
{
    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function navLinksForm(Request $request, int $id_menu)
    {
        $nav_links_form = $this->createForm(NavLinksFormType::class);
        $nav_links_form->handleRequest($request);

        if ($nav_links_form->isSubmitted() && $nav_links_form->isValid()) {
            // $em = $doctrine->getManager();
            $menu = $this->em->getRepository(Menu::class)->findOneBy(['pos' => $id_menu]);

            $pagesToInsert = [];
            $postsToInsert = [];
            $cusName = $nav_links_form->get('cus_name')->getData();
            $cusLink = $nav_links_form->get('cus_link')->getData();

            // Collect pages and posts data to be inserted
            $pages = $nav_links_form->get('pages')->getData();
            $posts = $nav_links_form->get('posts')->getData();

            foreach ($pages as $link) {
                $oldLink = $this->em->getRepository(MenuLink::class)->findOneBy([
                    'menu' => $menu->getId(),
                    'page' => $link
                ]);

                if (!$oldLink) {
                    $pagesToInsert[] = $link;
                }
            }

            foreach ($posts as $link) {
                $oldLink = $this->em->getRepository(MenuLink::class)->findOneBy([
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
                $menuLink->setCusLink([$cusLink]);
                $menuLinksToInsert[] = $menuLink;
            }

            // Batch insert all menu links
            foreach ($menuLinksToInsert as $menuLink) {
                $this->em->persist($menuLink);
            }

            $this->em->flush();
        }

        return $nav_links_form;
    }

    private function navSelectForm(Request $request)
    {
        $nav_select_form = $this->createForm(NavSelectFormType::class);
        $nav_select_form->handleRequest($request);

        if ($nav_select_form->isSubmitted() && $nav_select_form->isValid()) {
            $id_menu = $nav_select_form->get('nav_select')->getData()->getId();
        }

        return $nav_select_form;
    }

    private function navCreateForm(Request $request)
    {
        $menu = new Menu();
        $nav_create_form = $this->createForm(NavCreateFormType::class, $menu);
        $nav_create_form->handleRequest($request);
        if ($nav_create_form->isSubmitted() && $nav_create_form->isValid()) {
            $this->em->persist($menu);
            $this->em->flush();
        }

        return $nav_create_form;
    }

    private function initPage(Request $request, string $title = '', int $id_menu = 0)
    {
        $nav_links_form = $this->navLinksForm($request, $id_menu);
        $nav_select_form = $this->navSelectForm($request);
        $nav_create_form = $this->navCreateForm($request);

        $menusBase = $this->em->getRepository(Menu::class);
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
    public function index(Request $request): Response
    {
        return $this->initPage($request, "Navigation du site");
    }

    #[Route('/admin/navigation/menu', name: 'app_admin_nav_menu')]
    public function createMenu(Request $request): Response
    {
        return $this->initPage($request, "Créer un nouveau menu");
    }

    #[Route('/admin/navigation/{id_menu}', name: 'app_admin_nav_select')]
    public function navSelected(Request $request, int $id_menu): Response
    {
        return $this->initPage($request, "Navigation du site", $id_menu);
    }

    #[Route('/admin/navigation/manage-link/{id_link}', name: 'app_admin_nav_manage_link')]
    public function manageLink(Request $request, int $id_link)
    {
        $menuLink = $this->em->getRepository(MenuLink::class)->find($id_link);
        $form = $this->createForm(NavUpdateLinkFormType::class, $menuLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = [
                $form->get('cus_name')->getData()
            ];
            $link = [
                $form->get('cus_link')->getData()
            ];
            $menuLink->setCusName($name);
            $menuLink->setCusLink($link);
            $this->em->persist($menuLink);
            $this->em->flush();
        }

        return $this->render('admin_navigation/popup.html.twig', [
            'form' => $form,
            'name' => $menuLink->getCusName(),
            'link' => $menuLink->getCusLink(),
        ]);
    }

    #[Route(path: '/delete-nav', name: 'delete_link', methods: ['POST'])]
    public function deleteNav(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $menu = $this->em->getRepository(Menu::class)->findOneBy(['id' => $data]);
        $menuItems = $this->em->getRepository(MenuLink::class)->findBy(['menu' => $menu->getId()]);

        foreach ($menuItems as $item) {
            if ($item) {
                $this->em->remove($item);
            }
        }

        $this->em->remove($menu);
        $this->em->flush();
    }

    #[Route(path: '/order-nav-link', name: 'order_nav_link', methods: ['POST'])]
    public function orderNavLink(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $item) {
            $link = $this->em->getRepository(MenuLink::class)->findOneBy(['id' => $item['id']]);
            if ($link) {
                if ($item['sublist']) {
                    $sublist = $this->em->getRepository(MenuLink::class)->findOneBy(['id' => $item['sublist']]);
                    $link->setParent($sublist);
                }
                $link->setOrderLink($item['order']);
                $this->em->persist($link);
            }
        }

        $this->em->flush();
        return new JsonResponse(['message' => 'Ordre des liens enregistré avec succès.']);
    }

    #[Route(path: '/delete-nav-link', name: 'delete_nav_link', methods: ['POST'])]
    public function deleteNavLink(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $link = $this->em->getRepository(MenuLink::class)->findOneBy(['id' => $data]);
        $this->em->remove($link);
        $this->em->flush();

        return new JsonResponse(['message' => 'Ordre des liens enregistré avec succès.']);
    }
}
