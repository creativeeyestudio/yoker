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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminNavigationController extends AbstractController
{
    private EntityManagerInterface $em;
    private $request;

    function __construct(EntityManagerInterface $em, RequestStack $request)
    {
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    private function navLinksForm(int $id_menu) : FormInterface
    {
        $nav_links_form = $this->createForm(NavLinksFormType::class);
        $nav_links_form->handleRequest($this->request);

        if ($nav_links_form->isSubmitted() && $nav_links_form->isValid()) {
            $menu = $this->em->getRepository(Menu::class)->findOneBy(['pos' => $id_menu]);

            $pagesToInsert = [];
            $postsToInsert = [];
            $cusName = $nav_links_form->get('cus_name')->getData();
            $cusLink = $nav_links_form->get('cus_link')->getData();

            // Collect pages and posts data to be inserted
            $pages = $nav_links_form->get('pages')->getData();
            $posts = $nav_links_form->get('posts')->getData();

            $this->findAndStoreLink($pages, $menu->getId(), 'page', $pagesToInsert, $this->em);
            $this->findAndStoreLink($posts, $id_menu, 'post', $postsToInsert, $this->em);

            // Combine insertions
            $menuLinksToInsert = [];

            foreach ($pagesToInsert as $link) {
                $menuLinksToInsert[] = $this->createMenuLink($menu, $link, $link, 'page');
            }

            foreach ($postsToInsert as $link) {
                $menuLinksToInsert[] = $this->createMenuLink($menu, $link, $link, 'post');
            }

            // Insert custom link if both name and link are provided
            if ($cusName && $cusLink) {
                $menuLinksToInsert[] = $this->createMenuLink($menu, $cusName, $cusLink, 'custom');
            }

            // Batch insert all menu links
            foreach ($menuLinksToInsert as $menuLink) {
                $this->em->persist($menuLink);
            }

            $this->em->flush();
        }

        return $nav_links_form;
    }

    private function findAndStoreLink($links, $menuId, $entityType, &$toInsert, $entityManager)
    {
        foreach ($links as $link) {
            $oldLink = $entityManager->getRepository(MenuLink::class)->findOneBy([
                'menu' => $menuId,
                $entityType => $link
            ]);

            if (!$oldLink) {
                $toInsert[] = $link;
            }
        }
    }

    private function createMenuLink($menu, $name, $link, $entityType = null) : MenuLink
    {
        $menuLink = new MenuLink();
        $menuLink->setMenu($menu);
        $menuLink->setOrderLink(0);

        if ($entityType === 'page') {
            $menuLink->setCusName($name->getPageName());
            $menuLink->setPage($link);
        } elseif ($entityType === 'post') {
            $menuLink->setCusName($name->getPostName());
            $menuLink->setPost($link);
        } elseif ($entityType === 'custom') {
            $menuLink->setCusName([$name]);
            $menuLink->setCusLink([$link]);
        }

        return $menuLink;
    }

    private function navSelectForm() : FormInterface
    {
        $nav_select_form = $this->createForm(NavSelectFormType::class);
        $nav_select_form->handleRequest($this->request);

        if ($nav_select_form->isSubmitted() && $nav_select_form->isValid()) {
            $id_menu = $nav_select_form->get('nav_select')->getData()->getId();
        }

        return $nav_select_form;
    }

    private function navCreateForm() : FormInterface
    {
        $menu = new Menu();
        $nav_create_form = $this->createForm(NavCreateFormType::class, $menu);
        $nav_create_form->handleRequest($this->request);
        if ($nav_create_form->isSubmitted() && $nav_create_form->isValid()) {
            $this->em->persist($menu);
            $this->em->flush();
        }

        return $nav_create_form;
    }

    private function initPage(string $title, int $id_menu = 0) : Response
    {
        $nav_links_form = $this->navLinksForm($id_menu);
        $nav_select_form = $this->navSelectForm();
        $nav_create_form = $this->navCreateForm();

        $menusBase = $this->em->getRepository(Menu::class);
        $menus = $menusBase->findAll();
        $menu = $menusBase->findOneBy(['pos' => $id_menu]);

        $route_name = $this->request->attributes->get('_route');

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
    public function index(): Response
    {
        return $this->initPage("Navigation du site");
    }

    #[Route('/admin/navigation/menu', name: 'app_admin_nav_menu')]
    public function createMenu(): Response
    {
        return $this->initPage("Créer un nouveau menu");
    }

    #[Route('/admin/navigation/{id_menu}', name: 'app_admin_nav_select')]
    public function navSelected(int $id_menu): Response
    {
        return $this->initPage("Navigation du site", $id_menu);
    }

    #[Route('/admin/navigation/manage-link/{id_link}', name: 'app_admin_nav_manage_link')]
    public function manageLink(int $id_link) : Response
    {
        $menuLink = $this->em->getRepository(MenuLink::class)->find($id_link);
        $form = $this->createForm(NavUpdateLinkFormType::class, $menuLink);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = [
                $form->get('cus_name')->getData()
            ];
            $link = [
                $form->get('cus_link')->getData()
            ];
            $menuLink
                ->setCusName($name)
                ->setCusLink($link);
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
    public function deleteNav()
    {
        $data = json_decode($this->request->getContent(), true);
        $menu = $this->em->getRepository(Menu::class)->findOneBy(['id' => $data]);
        $menuItems = $this->em->getRepository(MenuLink::class)->findBy(['menu' => $menu->getId()]);

        foreach ($menuItems as $item) {
            if ($item) $this->em->remove($item);
        }

        $this->em->remove($menu);
        $this->em->flush();
    }

    #[Route(path: '/order-nav-link', name: 'order_nav_link', methods: ['POST'])]
    public function orderNavLink() : JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

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
        return $this->json(['message' => 'Ordre des liens enregistré avec succès.']);
    }

    #[Route(path: '/delete-nav-link', name: 'delete_nav_link', methods: ['POST'])]
    public function deleteNavLink() : JsonResponse
    {
        $data = json_decode($this->request->getContent(), true);

        $link = $this->em->getRepository(MenuLink::class)->findOneBy(['id' => $data]);
        $this->em->remove($link);
        $this->em->flush();

        return $this->json(['message' => 'Ordre des liens enregistré avec succès.']);
    }
}
