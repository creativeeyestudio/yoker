<?php

namespace App\Services;

use App\Form\CommonBlockFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class CommonBlocksService extends AbstractController {

    function BlockManager(Request $request, String $filePath, String $filePathEn){
        $form = $this->createForm(CommonBlockFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $filesystem = new Filesystem();

            // FR
            $filesystem->dumpFile($filePath, $data['common_block']);

            // EN
            $filesystem->dumpFile($filePathEn, $data['common_block_en']);
        }

        return $form;
    }
}