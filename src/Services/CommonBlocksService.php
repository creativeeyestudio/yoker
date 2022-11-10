<?php

namespace App\Services;

use App\Form\CommonBlockFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class CommonBlocksService extends AbstractController {

    function BlockManager(Request $request, String $filePath){
        $form = $this->createForm(CommonBlockFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $filesystem = new Filesystem();
            $filesystem->remove([$filePath]);
            $file = fopen($filePath, 'w');
            fwrite($file, $data['common_block']);
        }

        return $form;
    }

    

}