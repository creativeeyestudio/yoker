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
            $filesystem->remove([$filePath]);
            $file = fopen($filePath, 'w');
            fwrite($file, $data['common_block']);
            // EN
            $filesystem->remove([$filePathEn]);
            $file = fopen($filePathEn, 'w');
            fwrite($file, $data['common_block_en']);
        }

        return $form;
    }

    

}