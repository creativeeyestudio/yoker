<?php

namespace App\Controller;

use App\Entity\CodeWeave;
use App\Entity\CodeWeaverFiles;
use App\Form\CodeWeaveType;
use Doctrine\ORM\EntityManagerInterface;
use MatthiasMullie\Minify;
use ScssPhp\ScssPhp\Compiler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExtCodeWeaveController extends AbstractController
{
    private $em;
    private $repo;
    private $repoFiles;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(CodeWeave::class);
        $this->repoFiles = $this->em->getRepository(CodeWeaverFiles::class);
    }

    #[Route('/admin/codeweave', name: 'codeweave')]
    public function index(): Response
    {
        $cssList = $this->repo->findBy(['type' => 0]);
        $jsList = $this->repo->findBy(['type' => 1]);

        return $this->render('ext_codeweave/index.html.twig', [
            'title' => "CodeWeave - Gestion des fichiers CSS et JS",
            'cssList' => $cssList,
            'jsList' => $jsList
        ]);
    }

    #[Route(path: '/admin/codeweave/css', name: 'codeweave_create_file_css')]
    #[Route(path: '/admin/codeweave/js', name: 'codeweave_create_file_js')]
    #[Route(path: '/admin/codeweave/css/{id}', name: 'codeweave_update_file_css')]
    #[Route(path: '/admin/codeweave/js/{id}', name: 'codeweave_update_file_js')]
    public function manage(Request $request, ?int $id = null): Response
    {
        // Récupérer le nom de la route actuelle depuis l'objet Request
        $routeName = $request->attributes->get('_route');

        // Mapper les noms de route à des configurations spécifiques
        $routeTypeMap = [
            "codeweave_create_file_css" => [
                'type' => 0,
                'label' => "CSS",
                'mode' => 'text/x-scss'
            ],
            "codeweave_create_file_js" => [
                'type' => 1,
                'label' => "JS",
                'mode' => 'javascript'
            ],
            "codeweave_update_file_css" => [
                'type' => 0,
                'label' => "CSS",
                'mode' => 'text/x-scss'
            ],
            "codeweave_update_file_js" => [
                'type' => 1,
                'label' => "JS",
                'mode' => 'javascript'
            ],
        ];

        // Initialiser le formulaire en fonction du type de route actuelle
        $code = ($id !== null) ? $this->repo->find($id) : new CodeWeave();
        $form = $this->createForm(CodeWeaveType::class, $code);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code->setType($routeTypeMap[$routeName]['type']);
            $this->em->persist($code);
            $this->em->flush();

            $this->compileSCSS();
            $this->compileJS();

            return $this->redirectToRoute('codeweave');
        }

        // Déterminer le titre en fonction du type de route actuelle
        $action = ($id !== null) ? 'Modifier' : 'Créer';
        $title = sprintf('%s un fichier %s', $action, $routeTypeMap[$routeName]['label']);

        // Rendre la vue en utilisant le modèle Twig 'ext_codeweave/form-manager.html.twig'
        return $this->render('ext_codeweave/form-manager.html.twig', [
            'title' => $title,
            'mode' => $routeTypeMap[$routeName]['mode'],
            'form' => $form->createView(),
        ]);
    }


    #[Route(path: '/admin/codeweave/suppr/{id}', name: 'codeweave_delete_file')]
    public function delete(int $id)
    {
        $fileToDel = $this->repo->findOneBy(['id' => $id]);
        $this->em->remove($fileToDel);
        $this->em->flush();

        return $this->redirectToRoute('codeweave');
    }

    private function compileSCSS()
    {
        $scssFiles = $this->repo->findBy(['type' => 0], ['nom' => "ASC"]);
        $this->compileAndSave($scssFiles, 'css', Compiler::class);
    }

    private function compileJS()
    {
        $jsFiles = $this->repo->findBy(['type' => 1]);
        $this->compileAndSave($jsFiles, 'js', Minify\JS::class);
    }

    private function compileAndSave(array $files, $type, $classAction)
    {
        // On assemble le code
        $code = '';
        $minCode = '';
        foreach ($files as $item) {
            $code .= $item->getCode();
        }

        // On compile le code
        if ($type === 'css') {
            $compiler = new $classAction();
            $compiler->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
            $minCode = $compiler->compileString($code)->getCss();
        } elseif ($type === 'js') {
            $minifier = new $classAction();
            $minCode = $minifier->add($code)->minify();
        }

        // On crée le fichier
        $fileDir = $this->getParameter('css_js_directory');
        $fileName = $this->getParameter('css_js_path');
        $filePath = $fileDir . '/' . $fileName . '.' . $type;
        file_put_contents($filePath, $minCode);

        // On enregistre le fichier dans la DB
        $dbSave = $this->repoFiles->find(1);
        if (!$dbSave) {
            $dbSave = new CodeWeaverFiles();
        }

        if ($type === 'scss') {
            $dbSave->setCssFile($fileName);
        } else if ($type === 'js') {
            $dbSave->setJsFile($fileName);
        }

        $this->em->persist($dbSave);
        $this->em->flush();
    }
}
